<?php
    
    namespace WPKit\WpLoginAuth;

	class WpLoginAuth {
		
		/**
	     * The instance of the middleware
	     *
	     * @var \WPKit\WpLoginAuth\WpLoginAuth
	     */
	    protected static $instance = null;
	    
	    /**
	     * The settings of the middleware
	     *
	     * @var array
	     */
	    protected $settings = array();
	    
	    /**
	     * The constructor
	     *
	     * @param array $settings
	     * @return void
	     */
	    public function __construct($settings = array()) {
		    
		    $this->mergeSettings($settings);
		    
	    }
	    
	    /**
	     * Boot the middleware
	     *
	     * @param array $settings
	     * @return void
	     */
	    public static function boot($settings = array()) {
		    
		    if( ! static::$instance ) {
			    
			    static::$instance = new static($settings);
			    
		    }
		    
		    return static::$instance->start();
		    
	    }
	    
	    /**
	     * Start the middleware
	     *
	     * @return void
	     */
	    public function start()
	    {
		    
		    add_filter( 'login_url', array($this, 'getLoginUrl'), 10, 3);
			add_action( 'login_init', array($this, 'maskLogin') );
			add_filter( 'login_redirect', array($this, 'loginRedirect'), 10, 3 );
			
			nocache_headers();
			
			$is_allowed = $this->isAllowed();

            if ( ! $is_allowed ) {
                
                $current_url = get_current_url();
                
                wp_redirect( add_query_arg('redirect_to', urlencode($current_url), DS . ltrim( $this->settings['logout_redirect'], DS ) ) );
                
                exit();
                
            }
		    
		    return $is_allowed;
	        
	    }
    	
    	/**
	     * Merge settings into stored settings
	     *
	     * @param array $settings
	     * @return array
		 */
    	public function mergeSettings($settings = array()) {
	    	
	    	return $this->settings = array_merge(array(
    			'allow' => array(),
    			'disallow' => array(),
    			'logout_redirect' => '/wp-login.php',
    			'login_redirect' => home_url(),
    			'mask_wp_login' => false
			), $settings);

		}
		
		/**
	     * Check if current route is allowed
	     *
	     * @return boolean
		 */
		public function isAllowed() {
			
			$settings = $this->settings;
			
			extract($settings);
	    	
	    	if( ! $mask_wp_login && is_wp_login() ) {
		    	
		    	return true;
		    	
	    	}
	    	
	    	$is_allowed = is_user_logged_in() || is_page( ltrim( $settings['logout_redirect'], DS ) ) || is_route( $settings['logout_redirect'] );
			
			if( ! $is_allowed ) {
				
				if( ! empty( $settings['disallow'] ) ) {
					
					$is_allowed = true;
					
					foreach($settings['disallow'] as $page) {
	    			
		    			$is_allowed = is_page( $page ) || is_route( $page ) ? false : $is_allowed;
		    			
		    			if( ! $is_allowed ) {
			    			
			    			break;
			    			
		    			}
		    			
					}
				
				} else {
					
					foreach($settings['allow'] as $page) {
	    			
		    			$is_allowed = is_page( $page ) || is_route( $page ) ? true : $is_allowed;
		    			
		    			if( $is_allowed ) {
			    			
			    			break;
			    			
		    			}
		    			
					}
					
				}
				
			}
			
			return $is_allowed;
	    	
    	}
    	
    	/**
	     * Get login url, check if masked
	     *
	     * @param string $login_url
	     * @param string $redirect
	     * @param boolean $force_reauth
	     * @return string
		 */
    	public function getLoginUrl($login_url, $redirect, $force_reauth) {
	    	
	    	$settings = $this->settings;
        		
    		extract($settings);
			
			if( $logout_redirect && $mask_wp_login ) {
				
				$login_url = home_url($logout_redirect);

            	if ( ! empty($redirect) )
            		$login_url = add_query_arg('redirect_to', urlencode($redirect), $login_url);
            
            	if ( $force_reauth )
            		$login_url = add_query_arg('reauth', '1', $login_url);
            		
            	
    			
			}
    		
    		return $login_url;
    		
		}
        
        /**
	     * Get login redirect
	     *
	     * @return string
		 */
        public function loginRedirect() {
	        
	        $settings = $this->settings;
        		
    		extract($settings);
			
			return ! empty( $_REQUEST['redirect_to'] ) ? $_REQUEST['redirect_to'] : $loginRedirect;
			
		}
        
        /**
	     * Mask login, redirect user to custom login screen
	     *
	     * @return void
		 */
        public function maskLogin() {
	        
	        $settings = $this->settings;
        		
    		extract($settings);
	        
	        if( $mask_wp_login && is_wp_login() && empty ( $_REQUEST['interim-login'] ) ) {
	            
	            $args = array();
	            
	            if( ! empty( $_REQUEST['redirect_to'] ) ) {
		            
		            $args['redirect_to'] = $_REQUEST['redirect_to'];
		            
	            }
                
                wp_redirect( add_query_arg( $args, $logout_redirect ) );
                
                exit();
                
            }
	        
        }
    	
    }
