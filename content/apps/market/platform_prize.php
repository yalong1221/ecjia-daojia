<?php
//
//    ______         ______           __         __         ______
//   /\  ___\       /\  ___\         /\_\       /\_\       /\  __ \
//   \/\  __\       \/\ \____        \/\_\      \/\_\      \/\ \_\ \
//    \/\_____\      \/\_____\     /\_\/\_\      \/\_\      \/\_\ \_\
//     \/_____/       \/_____/     \/__\/_/       \/_/       \/_/ /_/
//
//   上海商创网络科技有限公司
//
//  ---------------------------------------------------------------------------------
//
//   一、协议的许可和权利
//
//    1. 您可以在完全遵守本协议的基础上，将本软件应用于商业用途；
//    2. 您可以在协议规定的约束和限制范围内修改本产品源代码或界面风格以适应您的要求；
//    3. 您拥有使用本产品中的全部内容资料、商品信息及其他信息的所有权，并独立承担与其内容相关的
//       法律义务；
//    4. 获得商业授权之后，您可以将本软件应用于商业用途，自授权时刻起，在技术支持期限内拥有通过
//       指定的方式获得指定范围内的技术支持服务；
//
//   二、协议的约束和限制
//
//    1. 未获商业授权之前，禁止将本软件用于商业用途（包括但不限于企业法人经营的产品、经营性产品
//       以及以盈利为目的或实现盈利产品）；
//    2. 未获商业授权之前，禁止在本产品的整体或在任何部分基础上发展任何派生版本、修改版本或第三
//       方版本用于重新开发；
//    3. 如果您未能遵守本协议的条款，您的授权将被终止，所被许可的权利将被收回并承担相应法律责任；
//
//   三、有限担保和免责声明
//
//    1. 本软件及所附带的文件是作为不提供任何明确的或隐含的赔偿或担保的形式提供的；
//    2. 用户出于自愿而使用本软件，您必须了解使用本软件的风险，在尚未获得商业授权之前，我们不承
//       诺提供任何形式的技术支持、使用担保，也不承担任何因使用本软件而产生问题的相关责任；
//    3. 上海商创网络科技有限公司不对使用本产品构建的商城中的内容信息承担责任，但在不侵犯用户隐
//       私信息的前提下，保留以任何方式获取用户信息及商品信息的权利；
//
//   有关本产品最终用户授权协议、商业授权与技术服务的详细内容，均由上海商创网络科技有限公司独家
//   提供。上海商创网络科技有限公司拥有在不事先通知的情况下，修改授权协议的权力，修改后的协议对
//   改变之日起的新授权用户生效。电子文本形式的授权协议如同双方书面签署的协议一样，具有完全的和
//   等同的法律效力。您一旦开始修改、安装或使用本产品，即被视为完全理解并接受本协议的各项条款，
//   在享有上述条款授予的权力的同时，受到相关的约束和限制。协议许可范围以外的行为，将直接违反本
//   授权协议并构成侵权，我们有权随时终止授权，责令停止损害，并保留追究相关责任的权力。
//
//  ---------------------------------------------------------------------------------
//

defined('IN_ECJIA') or exit('No permission resources.');

/**
 * 营销活动抽奖记录
 */
class platform_prize extends ecjia_platform
{
    public function __construct()
    {
        parent::__construct();

        //Ecjia\App\Market\Helper::assign_adminlog_content();
        
        /* 加载全局 js/css */
        RC_Script::enqueue_script('jquery-validate');
        RC_Script::enqueue_script('jquery-uniform');
        RC_Style::enqueue_style('uniform-aristo');
        RC_Script::enqueue_script('jquery-form');
        RC_Script::enqueue_script('jquery-validate');
        RC_Script::enqueue_script('smoke');
        RC_Script::enqueue_script('bootstrap-placeholder');
		
  		RC_Style::enqueue_style('platform_market_activity', RC_App::apps_url('statics/platform-css/platform_market_activity.css', __FILE__));
        RC_Style::enqueue_style('prize', RC_App::apps_url('statics/platform-css/prize.css', __FILE__));
        
        RC_Script::enqueue_script('platform_activity', RC_App::apps_url('statics/platform-js/platform_activity.js', __FILE__), array(), false, true);
        RC_Script::localize_script('platform_activity', 'js_lang', RC_Lang::get('market::market.js_lang'));
        
        ecjia_platform_screen::get_current_screen()->add_nav_here(new admin_nav_here('抽奖记录', RC_Uri::url('market/platform_prize/init')));
        ecjia_platform_screen::get_current_screen()->set_subject('抽奖记录');
    }

    /**
     * 活动列表
     */
    public function init()
    {
        $this->admin_priv('activity_record_manage');

        $wechat_id = $this->platformAccount->getAccountID();
        $store_id = RC_DB::table('platform_account')->where('id', $wechat_id)->pluck('shop_id');
        
        $this->assign('ur_here', '抽奖记录');

        $list = [];
        $code_list = [];
        $activity_code = '';
        
        if (!empty($_GET['code'])) {
        	$activity_code = trim($_GET['code']);
        } else {
        	$menus = with(new Ecjia\App\Market\MarketPrizeMenu($store_id, $wechat_id))->getMenus();
        	if (!empty($menus)) {
        		foreach ($menus as $k => $menu) {
        			$code_list[$k]['code'] = $menu->action;
        		}
        		$default_code = $code_list['0']['code'];
        		$activity_code = $default_code;
        	}
        }
        
        ecjia_platform_screen::get_current_screen()->remove_last_nav_here();
        ecjia_platform_screen::get_current_screen()->add_nav_here(new admin_nav_here('抽奖记录'));
        ecjia_platform_screen::get_current_screen()->add_option('current_code', $activity_code);
        $this->assign('action_link', array('href' => RC_Uri::url('market/platform/activity_detail', array('code' => $activity_code)), 'text' => RC_Lang::get('market::market.back_activity_info')));
        
        if (!empty($activity_code)) {
            $factory = new Ecjia\App\Market\Factory();
            $activity_info = $factory->driver($activity_code);
            $activity_detail['code'] = $activity_info->getCode();
            $activity_detail['name'] = $activity_info->getName();
            $activity_detail['description'] = $activity_info->getDescription();
            $activity_detail['icon'] = $activity_info->getIcon();
            $this->assign('activity_detail', $activity_detail);
            $info = RC_DB::table('market_activity')->where('activity_group', $activity_code)->where('store_id', $_SESSION['store_id'])->where('wechat_id', $wechat_id)->where('enabled', 1)->first();
            if (!empty($info)) {
                $info['start_time'] = RC_Time::local_date('Y-m-d H:i', $info['start_time']);
                $info['end_time']   = RC_Time::local_date('Y-m-d H:i', $info['end_time']);
                $this->assign('info', $info);
            }
        }
        $list = $this->get_activity_record_list($info['activity_id']);

        $this->assign('activity_record_list', $list);
        $this->assign('code', $activity_code);
       
        $this->display('prize_record.dwt');
    }
	
	/**
	 * 获取活动抽奖记录
	 * @return array
	 */
	private function get_activity_record_list($activity_id = 0) {
		$db_activity_log = RC_DB::table('market_activity_log');
	
		if (!empty($activity_id)) {
			$db_activity_log->where('activity_id', $activity_id);
		}
	
		$count = $db_activity_log->count();
		$page = new ecjia_platform_page($count, 15, 5);
		$res   = $db_activity_log->where('activity_id', $activity_id)->orderBy('add_time', 'desc')->take(15)->skip($page->start_id-1)->get();
	
		if (!empty($res)) {
			foreach ($res as $key => $val) {
				$res[$key]['issue_time']  	= RC_Time::local_date('Y-m-d H:i:s', $res[$key]['issue_time']);
				$res[$key]['add_time']    	= RC_Time::local_date('Y-m-d H:i:s', $res[$key]['add_time']);
				$res[$key]['prize_type']	= RC_DB::table('market_activity_prize')->where('prize_id', $val['prize_type'])->pluck('prize_type');
			}
		}
		return array('item' => $res, 'page' => $page->show(), 'desc' => $page->page_desc(), 'current_page' => $page->current_page);
	}
}

//end