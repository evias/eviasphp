<?php

final class eVias_Service_Twitter
{

    private $_db = null;

    static private $_instance = null;

    private $_zendService = null;
    private $_accessToken = null;
    private $_authConfig  = null;

    private $_userName    = 'eVias';
    private $_user        = null;

    static public function getInstance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self;
        }

        return self::$_instance;
    }

    /**
     * PUBLIC API
     *
     * getTimelineXml
     * getFollowersXml
     * getLastPostXml
     * getUserIntroduction
     */

    public function getTimelineXml()
    {
        return (
            $this->_getService()
                ->status->userTimeline()
        );
    }

    public function getFollowersXml()
    {
        return (
            $this->_getService()
                ->user->followers()
        );
    }

    public function getLastPostXml()
    {
        return $this->_getService()->status->userTimeline()->status;
    }


    public function getUserIntro()
    {
        if (is_null($this->_user)) {

            $twitterUser = $this->_getService()->status->userTimeline()->user[0];

            $this->_user = $twitterUser;
        }

        $intro = $this->_user->name . ' (@' . $this->_user->screen_name . ')';

        return $intro;
    }


/** private **/



    private function __construct()
    {
        $this->_db = eVias_ArrayObject_Db::getDefaultAdapter();

        $this->_getService()->setLocalHttpClient(
            $this->_getAccessToken()->getHttpClient($this->_getAuthConfig())
        );
    }

    private function _getService()
    {
        if (is_null($this->_zendService)) {

            $this->_zendService = new Zend_Service_Twitter($this->_getAuthConfig);
        }

        return $this->_zendService;
    }

    private function _getAuthConfig()
    {
        if (is_null($this->_authConfig)) {

            $configFile = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', 'development');

            $oAuthConfig= $configFile->oauth;

            $consumerSecretQuery = $this->_db->select();
            $consumerSecretQuery->from('evias_twitter_auth', array('consumer_secret'))
                                ->where ('user_id = 1');

            $consumerSecret = $this->_db->fetchOne($consumerSecretQuery);

            $this->_authConfig = array(
                'callbackUrl'       => $oAuthConfig->callbackUrl,
                'siteUrl'           => $oAuthConfig->siteUrl,
                'consumerKey'       => $oAuthConfig->consumerKey,
                'consumerSecret'    => $consumerSecret,
                'username'          => $this->_userName
            );
        }

        return $this->_authConfig;
    }

    private function _getAccessToken()
    {
        if (is_null($this->_accessToken)) {

            $select = $this->_db->select();
            $select->from ('evias_twitter_auth', array('access_token', 'access_token_secret'))
                   ->where ('user_id = 1');

            $row = $this->_db->fetchRow($select);

            $zendObj = new Zend_Oauth_Token_Access();
            $zendObj->setToken($row['access_token'])
                    ->setTokenSecret($row['access_token_secret']);

            $this->_accessToken = $zendObj;
        }

        return $this->_accessToken;
    }
}

