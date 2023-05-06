# prolong-session

Install

1.Edit composer.json
    
        "repositories": [
            ....
            {
                "type": "git",
                "url": "https://github.com/KugutNikolay/yii2-prolong-session.git"
            }
        ]
        
2.Run command line
 
    composer require safepartner/yii2-prolong-session

3.User class need implements ProlongSessionInterface
        
    class User implements IdentityInterface, ProlongSessionInterface {

        	public function isEnabledProlongSession() {
        	    return true;
        	}        

        	public function getProlongSessionLogoutUrl() {
        	    return Url::to(['/logout']);
        	}
    }
    
4.Add config to main.php 
    
    	'bootstrap'				 => [
    	    ....
    		'prolong-session'
    	],
    	'modules'				 => [
    	    ....
            'prolong-session' => [
            			'class' => safepartner\prolongSession\Module::class,
            			'timeout' => 120,
            			'timeoutSendRequest' => 10,
            		],
            ]
        ],
        ....
        'components'		 	=> [
            'urlManager' => [
                'rules' => [
                    ....
                    '<module:prolong-session>/<action>'	=> '<module>/default/<action>',
                ]
            ]
        ]
        

