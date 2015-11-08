<?php
class SmsController extends Controller {
	public function filters() {
		return array();
	}
	
	public function actionLabyrintti() {
		$sms = new Sms;
		$sms->source = $_REQUEST['source'];
		$sms->operator = $_REQUEST['operator'];
		$sms->destination = $_REQUEST['dest'];
		$sms->keyword = stripslashes($_REQUEST['keyword']);
		$sms->header = $_REQUEST['header'];
		$sms->text = stripslashes($_REQUEST['text']);
		$sms->binary = $_REQUEST['binary'];
		$sms->timestamp = strtotime($_REQUEST['timestamp']);
		$sms->trySave();
		
		return $this->receiveSms($sms,stripslashes($_REQUEST['params']),function($msg){
			header('HTTP/1.1 200 OK');
			header('Content-Type: text/plain');
			echo 'text='.$msg;
		},function($msg){
			header('HTTP/1.1 200 OK');
			header('Content-Type: text/plain');
			echo 'text='.$msg;
			echo PHP_EOL.'error=yes';
		});
	}
	
	public function actionUsahidi() {
		$sms = new Sms;
		$sms->source = $_REQUEST['from'];
		if( preg_match('/^(\S+)\s+(.*)$/', $_REQUEST['message'], $regs) ){
			$sms->keyword = $regs[1];
			$sms->text = $regs[2];
		} else {
			$sms->text = $_REQUEST['message'];
		}
		$sms->timestamp = $_REQUEST['sent_timestamp']/1000;
		$sms->trySave();
		
		return $this->receiveSms($sms,$sms->text,function($msg){
			header('HTTP/1.1 200 OK');
			header("Content-type: application/json; charset=utf-8");
			echo json_encode(array(
				'payload'=>array(
					'success'=>'true',
					'error'=>null,
					'task'=>'send',
					'messages'=>array(
						array(
							'to'=>$_REQUEST['from'],
							'message'=>$msg,
							'uuid'=>$_REQUEST['message_id']
						)
					)
				)
			));
		},function($msg){
			header('HTTP/1.1 200 OK');
			header("Content-type: application/json; charset=utf-8");
			echo json_encode(array(
				'payload'=>array(
					'success'=>'true',
					'error'=>null,
					'task'=>'send',
					'messages'=>array(
						array(
							'to'=>$_REQUEST['from'],
							'message'=>$msg,
							'uuid'=>$_REQUEST['message_id']
						)
					)
				)
			));
		});
	}
	
	protected function receiveSms($sms, $msgText, $successFunction, $errorFunction) {
		$transaction = Yii::app()->db->beginTransaction();
		try {
			$wall = Wall::model()->find(
				"enablesms=TRUE AND UPPER(smskeyword)=UPPER(:keyword) AND (smsprefix IS NULL OR :text REGEXP CONCAT('^',smsprefix,'[[:space:]]'))",array(
					':keyword'=>trim($sms->keyword),
					':text'=>trim($msgText)
				)
			);
			
			if( is_null($wall) ) {
				return $errorFunction(g('Could not find the social wall you specified, please check the words in the beginning of your message (notice spaces).'));
			}
			
			if( !$wall->isPublished || $wall->hidden ) {
				return $errorFunction(g('This social wall has not been published or has been closed by the organizer.'));
			}
			
			if( $wall->isExpired ) {
				return $errorFunction(g('This wall has expired.').' '.g('No new messages or votes are allowed.'));
			}
			
			// Check available sms credits
			if( $wall->smscredit <= 0 ) {
				return $errorFunction(g('The social wall has unsufficient SMS credits, your message could not be received.'));
			}
			
			// Clear the prefix from the beginning of the message
			$text = preg_replace('/^'.$wall->smsprefix.'\s/i','',$msgText);
			
			// Check if the message is a vote or an ordinary message
			$poll = null;
			$choice = null;
			$choiceTable = PollChoice::getCharTable();
			foreach( $wall->Polls as $p ) {
				if( preg_match('/^'.$p->smsPrefix.'\s*(?P<choice>['.implode('',$choiceTable).'])\s*$/i',$text,$regs) ) {
					$poll = $p;
					$choice = PollChoice::model()->findByPk(array(
						'pollid'=>$poll->pollid,
						'choice'=>PollChoice::getChoiceNum($regs['choice'])
					));
				} else {
					foreach( $p->Choices as $c ) {
						if( preg_match('/^'.$p->smsPrefix.'\s*('.$c->char.'(\:)?\s*)?'.$c->text.'\s*$/i',$text,$regs) ) {
							$poll = $p;
							$choice = $c;
						}
					}
				}
				
				if( !is_null($poll) ) {
					if( is_null($choice) ) {
						$poll = null;
					} else {
						break;
					}
				}
			}
			
			if( is_null($poll) ) {
				// Check if there is a SMS-default question
				foreach( $wall->Questions as $q ) {
					if( $q->smsdefault ) {
						// Append question keyword to the beginning of the message
						$text = $q->keyword.' '.$text;
						break;
					}
				}
				
				$message = new Message;
				$message->wallid = $wall->wallid;
				$message->smsid = $sms->smsid;
				$message->timestamp = $sms->timestamp;
				$message->text = $text;
				$message->trySave();
				
				
				$successMsg = g('Thank you for your message!');
				if( $wall->premoderated ) {
					$successMsg .= ' '.g('Your message has been sent to the conversation wall {wall}.',array('{wall}'=>$wall->name));
				} else {
					$successMsg .= ' '.g('Your message has been sent to the conversation wall {wall}.',array('{wall}'=>$wall->name));
				}
				$successFunction($successMsg);
			} else {
        // Check if the voting has been closed
        if( $poll->closed ) {
          return $errorFunction(g('This poll has been closed, no more votes are accepted.'));
        }
        
				$senderhash = sha1('cnxsender_sms_'.$sms->source);
				$replyParams = array('{char}'=>$choice->char,'{choice}'=>$choice->text);
				if( $poll->limitchoices && $poll->hasOtherVotes($senderhash,$choice->choice) ) {
					$replyMessage = g('You have changed your vote to {char}: {choice}.',$replyParams)
						.' '.g('Thank you for your vote!');
				} else if( $poll->limitvotes && $poll->hasVotes($senderhash,$choice->choice) ) {
					$replyMessage = g('You have already voted {char}: {choice}.',$replyParams).' ';
					if( $poll->limitchoices ) {
						$replyMessage .= g('You can vote only once in this poll.');
					} else {
						$replyMessage .= g('You can vote this choice only once.');
					}
				} else {
					$replyMessage = g('You have voted {char}: {choice}.',$replyParams)
						.' '.g('Thank you for your vote!');
				}
				$poll->vote($choice->choice,$senderhash);
				$successFunction($replyMessage);
			}
			
			// All SMS (messages and votes) are takend from the wall SMS credits
			$wall->saveAttributes(array(
				'smscredit'=>$wall->smscredit-1
			));
			
			$transaction->commit();
		} catch( Exception $ex ) {
			$transaction->rollback();
			Yii::log($ex->getTraceAsString(),CLogger::LEVEL_ERROR,'sms');
			return $errorFunction(g('An unexpexted error occurred, the message could not be sent.'));
		}
		
		if( is_null($poll) ) {
			if( $wall->enabletwitter ) {
				if( $wall->TwitterUser ) {
					Yii::app()->twitter->setUser($wall->TwitterUser);
				}
				Yii::app()->twitter->send($message,$wall);
			}
		}
	}
}
