<?php
class WallController extends Controller {
	public $layout = 'wall';
	protected $wall = null;
	protected $noAccess = false;
	const PSHS_SALT = 'areg2308ura+rg,areg1';
	
	public function filters() {
		return array(
			'setLanguage',
			'passwordCheck - password',
			'adminCheck + admin,adminPoll,adminPollDelete,adminQuestion,adminQuestionDelete,adminQuestionsInsert,messages,report,queue,approveMessage',
			'visibleCheck - unpublished,admin,adminPoll,adminPollDelete,adminQuestion,adminQuestionDelete',
		);
	}
	
	public function init() {
		parent::init();
		
		if( isset($this->actionParams['wall']) ) {
			$this->wall = Wall::model()->find('UPPER(name) LIKE UPPER(:name)',array(
				':name'=>$this->actionParams['wall']
			));
			if( is_null($this->wall) ) {
				throw new CHttpException(404,g('The specified social wall could not be found, please check the address'));
			}
		} else {
			return $this->redirect('site/index');
		}

		$this->cssFile('wall');

		// Select and set up theme according to wall
		if( !$this->wall->theme ) {
			throw new CHttpException(500,g('This social wall is not set up properly, please contact event organizer.'));
		}

		Yii::app()->theme = $this->wall->theme;
		$lessFile = Yii::app()->theme->basePath.'/less/wall.less';
		$lessc = Yii::app()->less->create();
		$lessc->setVariables($this->wall->ThemeModel->lessVariables());
		$css = $lessc->compileFile($lessFile);
		Yii::app()->clientScript->registerCss('themeWallLess',$css);

		// Set page title
		$this->pageTitle = strip_tags($this->wall->title);
		// Wall indexing
		if( !$this->wall->index ) {
			Yii::app()->clientScript->registerMetaTag('noindex,nofollow','robots');
		}

		if( $this->actionParams['embed'] && $this->actionParams['embed'] === $this->ehsh() ) {
			$this->layout = 'embed';
		}

		if( $this->wall->enabletwitter && $this->wall->TwitterUser ) {
			Yii::app()->twitter->setUser($this->wall->TwitterUser);
		}

		if( $this->wall->isExpired ) {
			if( !Yii::app()->request->isAjaxRequest ) {
				f(g('This wall has expired.').' '.g('No new messages or votes are allowed.'));
			}
		}
	}
	
	public function filterSetLanguage($filterChain) {
		if( isset($_GET['language']) && $this->isValidLanguage($_GET['language']) ) {
			Yii::app()->language = $_GET['language'];
		}
		
		$filterChain->run();
	}
	
	public function getWallNavItems($groupSubitemLimit=8,$showAdmin=true) {
		$items = array();
		
		if( $this->layout == 'view' ) {
			$conversationRoute = 'wall/view';
			$subRoute = 'View';
		} else {
			$conversationRoute = 'wall/index';
			$subRoute = '';
		}
		
		if( $this->noAccess === false ) {
			$questionItems = $this->getSubItems($this->wall->Questions,'question','question'.$subRoute,'question-sign');
			$pollItems = $this->getSubItems($this->wall->Polls,'poll','poll'.$subRoute,'align-left');
			$subItems = $questionItems + $pollItems;
			if( count($subItems) > 0 || $this->route != 'wall/index' ) {
				$items['wall'] = array(
					'class'=>'bootstrap.widgets.TbMenu',
					'items'=>array(
						array('label'=>g('Conversation'),'url'=>array($conversationRoute),'icon'=>'home'),
					)
				);
				if( count($questionItems) + count($pollItems) > $groupSubitemLimit ) {
					if( !empty($questionItems) ) {
						$items['wall']['items']['questions'] = array(
							'label'=>g('Questions'),
							'items'=>$questionItems
						);
					}
					if( !empty($pollItems) ) {
						$items['wall']['items']['polls'] = array(
							'label'=>g('Polls'),
							'items'=>$pollItems
						);
					}
				} else {
					ksort($subItems);
					$items['wall']['items'] = array_merge($items['wall']['items'],$subItems);
				}
			}

			if( $showAdmin ) {
				$adminItems = array();
				if( $this->layout != 'view' ) {
					if( $this->isWallAdmin() && !preg_match('/^wall\/admin/i',$this->route) ) {
						$adminItems[] = array(
							'label'=>'<span class="label label-info">'.g('Using admin view').'</span>',
							'url'=>array('wall/admin')
						);
					}
					$adminItems[] = array('label'=>g('Wall admin'),'url'=>array('wall/admin'),'icon'=>'wrench');
				} else {
					if( $this->wall->showPremiumFeatures ) {
						if( $this->action->id == 'visualize' ) {
							$adminItems[] = array(
								'label'=>'<i class="fa fa-pause"></i>',
								'url'=>'#',
								'linkOptions'=>array(
									'class'=>'pause-visualization',
									'data-title-pause'=>g('Pause'),
									'data-title-resume'=>g('Resume'),
								)
							);
						}
						$adminItems[] = array(
							'label'=>'<i class="fa fa-caret-up"></i>',
							'url'=>'#',
							'linkOptions'=>array(
								'class'=>'hide-menu',
								'data-title-hide'=>g('Hide menu'),
								'data-title-show'=>g('Show menu'),
							)
						);
					}
					$adminItems[] = array(
						'label'=>'<i class="fa fa-times"></i>',
						'url'=>array('wall/index'),
						'linkOptions'=>array(
							'rel'=>'tooltip',
							'title'=>g('Close screen view'),
							'data-placement'=>'bottom'
						)
					);
				}
				
				$items['walladmin'] = array(
					'class'=>'bootstrap.widgets.TbMenu',
					'htmlOptions'=>array('class'=>'pull-right'),
					'items'=>$adminItems,
					'encodeLabel'=>false
				);
			}
		}
		
		return $items;
	}
	
	protected function getSubItems($list,$type,$route,$icon=null) {
		$items = array();
		foreach( $list as $item ) {
			if( !is_null($item->position) ) {
				$items[$item->position.'_'.$type] = array('label'=>$item->title,'url'=>array($route,'search'=>$item->keyword),'icon'=>$icon);
			}
		}
		return $items;
	}
	
	public function actionIndex() {
		return $this->render('index');
	}
	
	public function actionView() {
		$this->layout = 'view';
		return $this->render('view');
	}
	
	public function actionVisualize() {
		$this->layout = 'view';
		return $this->render('visualize');
	}
	
	public function actionUnpublished() {
		return $this->render('unpublished');
	}
	
	public function actionReport($showQueued=false,$showDeleted=false) {
		$items = array(
			'0_c'=>array(
				'title'=>$this->wall->ThemeModel->conversationTitle,
				'type'=>null
			)
		);
		foreach( $this->wall->Questions as $item ) {
			if( $item->position ) {
				$itemKey = $item->position.'_q';
			} else {
				$itemKey = '_'.$item->keyword;
			}
			$items[$itemKey] = array(
				'title'=>$item->question,
				'type'=>'question',
				'id'=>$item->primaryKey
			);
		}
		foreach( $this->wall->Polls as $item ) {
			if( $item->position ) {
				$itemKey = $item->position.'_p';
			} else {
				$itemKey = '_'.$item->keyword;
			}
			$items[$itemKey] = array(
				'title'=>$item->question,
				'type'=>'poll',
				'id'=>$item->primaryKey,
				'poll'=>$item
			);
		}
		
		$where = array();
		if( $this->wall->enabletwitter ) {
			$where[] = 'Tweet.RetweetOf IS NULL';
		}
		if( $this->wall->premoderated && !$showQueued ) {
			$where[] = 'Messages.approved IS NOT NULL';
		}
		if( $showDeleted ) {
			Message::model()->showDeleted = true;
		}
		$messages = $this->wall->Messages(array(
			'order'=>'Messages.timestamp ASC',
			'condition'=>implode(' AND ',$where),
			'with'=>array('Tweet','Tweet.Media','Tweet.RetweetOf')
		));
		foreach( $messages as $message ) {
			$itemKey = '0_c';
			$msgQuestion = null;
			foreach( $this->wall->Questions as $question ) {
				if( preg_match($question->pattern,$message->text) ) {
					if( $question->position ) {
						$itemKey = $question->position.'_q';
					} else {
						$itemKey = '_'.$question->keyword;
					}
					$msgQuestion = $question;
					break;
				}
			}
			$items[$itemKey]['messages'][] = $message->toArray($msgQuestion);
		}
		
		ksort($items);
		
		$this->pageTitle = $this->wall->title.' - '.g('Report');
		return $this->render('report',compact('items','showDeleted','showQueued'));
	}
	
	public function actionMessages() {
		return $this->render('messages',array('title'=>g('All messages'),'showRemoved'=>false));
	}
	
	public function actionRemovedMessages() {
		return $this->render('messages',array('title'=>g('Removed messages'),'showRemoved'=>true));
	}
	
	public function actionQueue() {
		return $this->render('queue');
	}
	
	public function actionChat($cmd) {
		$question = null;
		if( isset($_REQUEST['question']) ) {
			if( $_REQUEST['question'] === 'all' ) {
				$question = true;
			} else {
				$question = Question::model()->findByPk($_REQUEST['question']);
				if( is_null($question) || $question->wallid != $this->wall->primaryKey ) {
					throw new CHttpException(404,'Invalid command');
				}
			}
		}
		
		switch($cmd){
			case 'getFeed': {
				if( $_REQUEST['queue'] && $this->isWallAdmin() ) {
					$queue = true;
				} else {
					$queue = false;
				}
				$onlyDeleted =  $_REQUEST['removed'] && $this->isWallAdmin();
				$messages = $this->getFeed($_REQUEST['count'],$_REQUEST['offset'],$_REQUEST['since'],$question,$_REQUEST['lastUpdate'],$queue,$onlyDeleted);
				return $this->renderJSON($messages);
			}
			case 'sendMessage': {
				if( $this->wall->isExpired ) {
					//f(g('This wall using period has expired. No new messages or votes are allowed.'));
					break;
				}
				$text = stripslashes($_REQUEST['message']);
				//$time = strtotime($_REQUEST['time']);
				$username = stripcslashes($_REQUEST['username']);
				$replyTo = $_REQUEST['reply_to'];
				$message = $this->sendMessage($text,null,$username,$replyTo,$question);
				$reply = array();
				if( $this->wall->premoderated ) {
					$reply['info'] = g('Your message has been received and is awaiting for approval.');
				} else {
					$reply['message'] = $message->toArray($question);
				}
				return $this->renderJSON($reply);
			}
			case 'registerUser': {
				if( $this->wall->isExpired ) {
					break;
				}
				$username = stripslashes($_REQUEST['username']);
				Yii::app()->user->nickname = $username;
				return $this->renderJSON(array('username'=>$username));
			}
			case 'deleteMessage': {
				if( $this->isWallAdmin() ) {
					$message = Message::model()->findByPk($_REQUEST['messageid']);
					if( !is_null($message) ) {
						if( $message->wallid == $this->wall->wallid ) {
							$message->delete();
							if( $this->wall->enabletwitter ) {
								Yii::app()->twitter->delete($message);
							}
							return $this->renderJSON(array('message'=>$message->toArray($question)));
						} else {
							throw new CHttpException(403,'Forbidden message id');
						}
					}
				}
				break;
			}
			case 'restoreMessage': {
				if( $this->isWallAdmin() ) {
					$messageModel = Message::model();
					$messageModel->showDeleted = true;
					$message = $messageModel->findByPk($_REQUEST['messageid']);
					$messageModel->showDeleted = false;
					if( !is_null($message) ) {
						if( $message->wallid == $this->wall->wallid ) {
							$message->undelete();
							if( $this->wall->enabletwitter && !$message->Tweet ) {
								// Tweets cannot be restored, resend message to Twitter
								Yii::app()->twitter->send($message,$this->wall);
							}
							return $this->renderJSON(array('message'=>$message->toArray($question)));
						} else {
							throw new CHttpException(403,'Forbidden message id');
						}
					}
				}
				break;
			}
			case 'pinMessage': {
				if( $this->isWallAdmin() ) {
					$message = Message::model()->findByPk($_REQUEST['messageid']);
					if( !is_null($message) ) {
						if( $message->wallid == $this->wall->wallid ) {
							$message->pin();
							$message->refresh();
							return $this->renderJSON(array('message'=>$message->toArray($question)));
						} else {
							throw new CHttpException(403,'Forbidden message id');
						}
					}
				}
				break;
			}
			case 'unpinMessage': {
				if( $this->isWallAdmin() ) {
					$message = Message::model()->findByPk($_REQUEST['messageid']);
					if( !is_null($message) ) {
						if( $message->wallid == $this->wall->wallid ) {
							$message->unpin();
							$message->refresh();
							return $this->renderJSON(array('message'=>$message->toArray($question)));
						} else {
							throw new CHttpException(403,'Forbidden message id');
						}
					}
				}
				break;
			}
			case 'approveMessage': {
				$message = Message::model()->findByPk($_REQUEST['messageid']);
				if( !is_null($message) ) {
					if( $message->wallid == $this->wall->wallid ) {
						$message->approve();
						if( $this->wall->enabletwitter && !$message->Tweet ) {
							// Queued messages are sent to twitter on approval
							Yii::app()->twitter->send($message,$this->wall);
						}
						return $this->renderJSON(array('message'=>$message->toArray($question)));
					} else {
						throw new CHttpException(403,'Forbidden message id');
					}
				}
				break;
			}
			default: {
				throw new CHttpException(404,'Invalid command');
			}
		}
	}
	
	public function actionQuestion($search=false,$id=false) {
		if( $id !== false && $search == false ) {
			$search = $id;
		}
		$question = Question::model()->findByAttributes(array(
			'keyword'=>urldecode($search),
			'wallid'=>$this->wall->primaryKey
		));
		if( is_null($question) ) {
			throw new CHttpException(404,'Invalid question id');
		}
		
		$this->pageTitle = $this->pageTitle.': '.$question->title;
		return $this->render('question',compact('question'));
	}
	
	public function actionPoll($search=false,$id=false,$cmd=false) {
		if( $id !== false && $search == false ) {
			$search = $id;
		}
		$poll = Poll::model()->findByAttributes(array(
			'keyword'=>urldecode($search),
			'wallid'=>$this->wall->primaryKey
		));
		if( is_null($poll) || $poll->wallid != $this->wall->primaryKey ) {
			throw new CHttpException(404,'Invalid poll id');
		}
		
		$senderhash = Yii::app()->user->getSenderHash();
		
		if( $cmd ) {
			switch($cmd){
				case 'getVotes': {
					$choices = array();
					foreach( $poll->Choices as $choice ) {
						$choices[$choice->choice] = $choice->voteCount;
					}
					return $this->renderJSON($choices);
				}
				case 'vote': {
					if( $this->wall->isExpired ) {
						break;
					}
          
          if( $poll->closed ) {
            break;
          }
					
					$transaction = Yii::app()->db->beginTransaction();
					
					$poll->vote($_REQUEST['choice'], $senderhash);
					
					$choices = $poll->Choices(array('index'=>'choice'));
					$choiceList = array();
					foreach( $choices as $choice ) {
						$choiceList[$choice->choice] = $choice->voteCount;
					}
					
					$transaction->commit();
					
					return $this->renderJSON($choiceList);
				}
				default: {
					throw new CHttpException(404,'Invalid command');
				}
			}
		}
		
		$myChoice = array();
		$myVotes = PollVote::model()->findAllByAttributes(array(
			'pollid'=>$poll->primaryKey,
			'senderhash'=>$senderhash
		));
		foreach( $myVotes as $myVote ) {
			$myChoice[] = $myVote->choice;
		}
		
		$this->pageTitle = $this->pageTitle.': '.$poll->title;
		return $this->render('poll',compact('poll','myChoice'));
	}
	
	public function actionAdmin() {
		$criteria = array(
			'criteria'=>array(
				'condition'=>'wallid=:wallid',
				'order'=>'position',
				'params'=>array(
					':wallid'=>$this->wall->primaryKey
				)
			)
		);
		
		$questions = new CActiveDataProvider('Question',$criteria);
		
		$polls = new CActiveDataProvider('Poll',$criteria);
		
		return $this->render('admin',compact('questions','polls'));
	}
	
	public function actionAdminLogout() {
		$this->revokeWallAdmin();
		
		return $this->redirect(array($this->id.'/index'));
	}
	
	public function actionAdminQuestion($id=false,$delete=false) {
		if( $this->wall->isExpired ) {
			f(g('This wall has expired.').' '.g('Cannot add or modify questions or polls.'),'error');
			return $this->redirect(array($this->id.'/admin'));
		}
			
		if( $id === false ) {
			$question = new Question;
			$question->wallid = $this->wall->primaryKey;
			$maxPos = 0;
			foreach( $this->wall->Questions as $q ) {
				$maxPos = max($q->position,$maxPos);
			}
			$question->position = $maxPos + 1;
		} else {
			$question = Question::model()->findByPk($id);
		}
		
		if( is_null($question) || $question->wallid != $this->wall->primaryKey ) {
			throw new CHttpException(404,'Invalid question id');
		}
		
		if( $_POST['Question'] ) {
			$question->attributes = $_POST['Question'];
			if( $question->save() ) {
				f(g('Question has been saved'),'success');
				return $this->redirect(array($this->id.'/admin'));
			}
		}
		
		return $this->render('adminQuestion',compact('question','delete'));
	}
	
	public function actionAdminQuestionDelete($id) {
		$question = Question::model()->findByPk($id);
		if( is_null($question) || $question->wallid != $this->wall->primaryKey ) {
			throw new CHttpException(404,'Invalid question id');
		}
		
		$question->delete();
	}
	
	public function actionAdminQuestionsInsert() {
		$questions = array();
		if( $_REQUEST['questions'] ) {
			$questionRows = explode(PHP_EOL,$_REQUEST['questions']);
			$valid = true;
			foreach( $questionRows as $questionRow ) {
				list($keyword,$title,$questionText) = explode(';',$questionRow);
				$question = new Question;
				$question->wallid = $this->wall->primaryKey;
				$question->keyword = $keyword;
				$question->title = $title;
				$question->question = $questionText;
				$question->position = null;
				$valid = $question->validate() && $valid;
				$questions[] = $question;
			}
			if( $valid ) {
				try {
					$transaction = Yii::app()->db->beginTransaction();
					foreach ($questions as $question) {
						$question->trySave();
					}
					$transaction->commit();
					f(g('Added {n} question|Added {n} questions',count($questions)));
					return $this->redirect(array($this->id.'/admin'));
				} catch (Exception $ex) {
					f($ex->getMessage(),'error');
				}
			}
		}
		
		return $this->render('adminQuestionsInsert',compact('questions'));
	}
	
	public function actionQuestionView($search=false,$id=false) {
		if( $id !== false && $search == false ) {
			$search = $id;
		}
		$question = Question::model()->findByAttributes(array(
			'keyword'=>urldecode($search),
			'wallid'=>$this->wall->primaryKey
		));
		if( is_null($question) ) {
			throw new CHttpException(404,'Invalid question id');
		}
		
		$this->layout = 'view';
		$this->pageTitle = $this->pageTitle.': '.$question->title;
		return $this->render('questionView',compact('question'));
	}
	
	public function actionAdminPoll($id=false,$delete=false) {
		if( $this->wall->isExpired ) {
			f(g('This wall has expired.').' '.g('Cannot add or modify questions or polls.'),'error');
			return $this->redirect(array($this->id.'/admin'));
		}
		
		if( $id === false ) {
			$poll = new Poll;
			$poll->wallid = $this->wall->primaryKey;
			$maxPos = 0;
			foreach( $this->wall->Polls as $p ) {
				$maxPos = max($p->position,$maxPos);
			}
			$poll->position = $maxPos + 1;
			$poll->keyword = $poll->position;
		} else {
			$poll = Poll::model()->findByPk($id);
		}
		
		if( is_null($poll) || $poll->wallid != $this->wall->primaryKey ) {
			throw new CHttpException(404,'Invalid poll id');
		}
		
		$oldChoices = $poll->Choices(array('index'=>'choice'));
		$choices = array();
		foreach( array_keys(PollChoice::getCharTable()) as $key ) {
			if( !array_key_exists($key,$oldChoices) ) {
				$pollChoice = new PollChoice;
				$pollChoice->choice = $key;
				$choices[$key] = $pollChoice;
			} else {
				$choices[$key] = $oldChoices[$key];
			}
		}
		
		if( $_POST['Poll'] ) {
			$poll->attributes = $_POST['Poll'];
			$transaction = Yii::app()->db->beginTransaction();
			$ok = $poll->save();
			foreach( $choices as $key => $choice ) {
				$choice->attributes = $_POST['PollChoice'][$key];
				if( !empty($choice->text) ) {
					if( $choice->isNewRecord ) {
						$choice->pollid = $poll->primaryKey;
					}
					if( $poll->clearVotes ) {
						$choice->votes = 0;
					}
					$ok = $choice->save() && $ok;
				} else {
					if( !$choice->isNewRecord ) {
						$choice->delete();
					}
				}
			}
			if( $poll->clearVotes ) {
				PollVote::model()->deleteAllByAttributes(array(
					'pollid'=>$poll->primaryKey,
				));
			}

			if( $ok ) {
				$transaction->commit();
				f(g('Poll has been saved'),'success');
				return $this->redirect(array($this->id.'/admin'));
			}
		}
		
		return $this->render('adminPoll',compact('poll','delete','choices'));
	}
	
	public function actionAdminPollDelete($id) {
		$poll = Poll::model()->findByPk($id);
		if( is_null($poll) || $poll->wallid != $this->wall->primaryKey ) {
			throw new CHttpException(404,'Invalid poll id');
		}
		
		$poll->delete();
	}
	
	public function actionPollView($search=false,$id=false) {
		if( $id !== false && $search == false ) {
			$search = $id;
		}
		$poll = Poll::model()->findByAttributes(array(
			'keyword'=>urldecode($search),
			'wallid'=>$this->wall->primaryKey
		));
		if( is_null($poll) ) {
			throw new CHttpException(404,'Invalid poll id');
		}
		
		$this->layout = 'view';
		$this->pageTitle = $this->pageTitle.': '.$poll->title;
		return $this->render('pollView',compact('poll'));
	}
	
	public function filterPasswordCheck($filterChain) {
		if( is_null($this->wall->password) ) {
			return $filterChain->run();
		}
		
		if( isset(Yii::app()->session['wallAccessTokens']) ) {
			$accessTokens = Yii::app()->session['wallAccessTokens'];
		} else {
			$accessTokens = array();
		}
		
		if( in_array($this->wall->primaryKey,$accessTokens) ) {
			return $filterChain->run();
		}
		
		$model = new PasswordForm;
		if( isset($_POST['PasswordForm']) ) {
			$model->attributes = $_POST['PasswordForm'];
			if( $model->validate() ) {
				if( $model->password == $this->wall->password ) {
					$accessTokens[] = $this->wall->primaryKey;
					Yii::app()->session['wallAccessTokens'] = $accessTokens;
					return $this->refresh();
				} else {
					$model->addError('password',g('Incorrect password'));
				}
			}
		} else if( preg_match('/phsh=([^&]*)/',Yii::app()->request->requestUri,$regs) ) {
			if( $regs[1] == crypt($this->wall->password,self::PSHS_SALT) ) {
				$accessTokens[] = $this->wall->primaryKey;
				Yii::app()->session['wallAccessTokens'] = $accessTokens;
				return $this->refresh();
			}
			$this->redirect(preg_replace('/phsh=([^&]*)/','',Yii::app()->request->requestUri));
		}
		
		$this->noAccess = true;
		return $this->render('password',compact('model'));
	}
	
	public function filterAdminCheck($filterChain) {
		if( $this->isWallAdmin() ) {
			return $filterChain->run();
		}
		
		$model = new PasswordForm;
		if( isset($_POST['PasswordForm']) ) {
			$model->attributes = $_POST['PasswordForm'];
			if( $model->validate() ) {
				if( $model->password == $this->wall->adminpassword ) {
					$this->grantWallAdmin();
					return $this->refresh();
				} else {
					$model->addError('password',g('Incorrect admin password'));
				}
			}
		}
		
		return $this->render('password',compact('model'));
	}
	
	public function filterVisibleCheck($filterChain) {
		// Check if the wall is visible
		if( !$this->wall->isPublished || $this->wall->hidden ) {
			if( $this->isWallAdmin() || $this->isWallOwner() ) {
				if( !Yii::app()->request->isAjaxRequest ) {
					if( $this->wall->isPublished ) {
						$msg = g('This wall has been hidden.');
						$msg .= ' '.g('It is not visible to visitors and messages are not being received.');
						$msg .= ' '.g('You can <a href="{url}">set this vall visible</a> to let the visitors see it.',array('{url}'=>$this->createUrl('client/wallShow',array('search'=>$this->wall->name,'from'=>'wall'))));
					} else {
						$msg = g('This wall has not been published.');
						$msg .= ' '.g('It is not visible to visitors and messages are not being received.');
						$msg .= ' '.g('You can <a href="{url}">publish this wall</a> to start using it.',array('{url}'=>$this->createUrl('client/wallPublish',array('search'=>$this->wall->name,'from'=>'wall'))));
					}
					f($msg);
				}
				$filterChain->run();
			} else {
				return $this->forward('wall/unpublished');
			}
		} else {
			$filterChain->run();
		}
	}
	
	protected function isWallAdmin() {
		if( isset(Yii::app()->session['wallAdminTokens']) ) {
			$accessTokens = Yii::app()->session['wallAdminTokens'];
		} else {
			$accessTokens = array();
		}
		
		if( in_array($this->wall->primaryKey,$accessTokens) ) {
			return true;
		}
		
		return false;
	}
	
	protected function isWallOwner() {
		return Yii::app()->user->client->primaryKey === $this->wall->clientid;
	}
	
	protected function grantWallAdmin() {
		if( isset(Yii::app()->session['wallAdminTokens']) ) {
			$accessTokens = Yii::app()->session['wallAdminTokens'];
		} else {
			$accessTokens = array();
		}
		$accessTokens[] = $this->wall->primaryKey;
		Yii::app()->session['wallAdminTokens'] = $accessTokens;
	}
	
	protected function revokeWallAdmin() {
		if( isset(Yii::app()->session['wallAdminTokens']) ) {
			$accessTokens = Yii::app()->session['wallAdminTokens'];
			Yii::app()->session['wallAdminTokens'] = array_diff($accessTokens,array($this->wall->primaryKey));
		}
	}
	
	/**
	 *
	 * @param integer $count
	 * @param integer $offset
	 * @param mixed $since
	 * @param Question $question
	 * @return type 
	 */
	protected function getFeed($count=20,$offset=0,$since=false,$question=null,$lastUpdate=null,$queue=false,$onlyDeleted=false) {
		if( $this->wall->enabletwitter && $this->wall->isPublished && !$this->wall->isExpired ) {
			Yii::app()->twitter->refreshFeed($this->wall);
		}
		
		$data = array(
			'inserted'=>array(),
			'deleted'=>array(),
			'pinned'=>array(),
			'lastUpdate'=>Yii::app()->db->createCommand('SELECT NOW()')->queryScalar()
		);
		
		$params = array();
		$where = array();
		
		$messageModel = Message::model();
		
		if( $onlyDeleted ) {
			$where[] = 'deleted IS NOT NULL';
			$messageModel->showDeleted = true;
		}
		
		if( $queue ) {
			$where[] = 'Messages.approved IS NULL';
		}
		
		if( $since && $since !== 'false' ) {
			if( $this->wall->premoderated && !$queue ) {
				$sinceMsg = $messageModel->findByPk($since);
				$params[':since'] = $sinceMsg->approved;
				$where[] = 'Messages.approved > :since';
			} else {
				$params[':since'] = $since;
				$where[] = 'Messages.messageid > :since';
			}
		} else {
			if( $this->wall->premoderated && !$queue ) {
				$where[] = 'Messages.approved IS NOT NULL';
			}
		}
		
		if( is_null($question) ) {
			foreach( $this->wall->Questions as $key => $q ) {
				$params[':q'.$key] = $q->getMysqlPattern();
				$where[] = 'Messages.text NOT REGEXP :q'.$key;
			}
		} else if( $question !== true ) {
			$params[':q'] = $question->getMysqlPattern();
			$where[] = 'Messages.text REGEXP :q';
		}
		
		if( $this->wall->enabletwitter ) {
			$where[] = 'Tweet.RetweetOf IS NULL';
		}
		
		$messages = $this->wall->Messages(array(
			'order'=>'Messages.timestamp DESC',
			'condition'=>implode(' AND ',$where),
			'limit'=>$count,
			'offset'=>$offset,
			'params'=>$params,
			'with'=>array('Tweet','Tweet.Media','Tweet.RetweetOf')
		));
		
		foreach( array_reverse($messages) as $message ) {
			$data['inserted'][] = $message->toArray($question);
		}
		
		if( !$onlyDeleted ) {
			$messageModel->showDeleted = true;
			$deleted = $messageModel->findAll(array(
				'condition'=>'wallid=:wallid AND deleted IS NOT NULL AND deleted > :last_update',
				'params'=>array(
					':wallid'=>$this->wall->wallid,
					':last_update'=>$lastUpdate
				)
			));
			$messageModel->showDeleted = false;
			foreach( $deleted as $deletedMessage ) {
				$data['deleted'][] = $deletedMessage->messageid;
			}
		}
		
		$pinned = $messageModel->findAll(array(
			'condition'=>'wallid=:wallid AND deleted IS NULL AND pinned IS NOT NULL',
			'params'=>array(
				':wallid'=>$this->wall->wallid,
			)
		));
		foreach( $pinned as $pinnedMessage ) {
			$data['pinned'][$pinnedMessage->messageid] = $pinnedMessage->pinned;
		}
		
		return $data;
	}
	
	protected function sendMessage($text,$time=null,$username=null,$replyTo=false,$question=null) {
		if( !is_null($question) ) {
			$text = $question->keyword.' '.stripslashes($text);
		}
		
		$message = new Message;
		
		$message->wallid = $this->wall->primaryKey;
		$message->text = $text;
		if( is_null($time) ) {
			$message->timestamp = new CDbExpression('NOW()');
		} else {
			$message->timestamp = $time;
		}
		$message->username = $username;
		if( $replyTo ) {
			$message->replyto = $replyTo;
		}
		if( $this->isWallAdmin() ) {
			$message->adminmessage = true;
		}
		
		$message->trySave();
		
		if( $this->wall->enabletwitter && !$this->wall->premoderated ) {
			Yii::app()->twitter->send($message,$this->wall);
		}
		
		$message->refresh();
		
		if( !is_null($question) ) {
			$message->text = preg_replace($question->getReplacePattern(),'',$message->text,1);
		}
		
		return $message;
	}
	
	public function checkWallParam($route,&$params) {
		$routeParts = explode('/',$route);
		if( count($routeParts) < 2 || $routeParts[0] == $this->id ) {
			if( !array_key_exists('wall',$params) ) {
				$params['wall'] = $this->wall->name;
			}
		}
	}
	
	public function createUrl($route, $params=array(), $ampersand='&') {
		$this->checkWallParam($route,$params);
		return parent::createUrl($route,$params,$ampersand);
	}
	
	public function createAbsoluteUrl($route, $params=array(), $schema='', $ampersand='&') {
		$this->checkWallParam($route,$params);
		return parent::createAbsoluteUrl($route,$params,$schema,$ampersand);
	}
	
	protected function phsh() {
		return crypt($this->wall->password,self::PSHS_SALT);
	}
	
	protected function ehsh() {
		return crypt('embedded',self::PSHS_SALT);
	}
}
