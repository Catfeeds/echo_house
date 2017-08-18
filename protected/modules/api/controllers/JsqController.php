<?php
class JsqController extends ApiController
{
    /**
     * 获取计算器利率
     * @param  string  $callback  js回调函数名称
     * @param  integer $year      年数
     * @param  integer $loan      贷款类型（1公积金贷款，2商业贷款）
     * @param  integer $rateStyle 商业贷款利率
     */
    public function actionGetRate()
    {
        $callback = Yii::app()->request->getQuery( 'callback' , '' );
		$year = Yii::app()->request->getPost( 'year' , 240 );
		$loan_type = Yii::app()->request->getPost( 'loan' , 1 );
		$rateStyle = Yii::app()->request->getPost( 'rateStyle' , 1 );

		$month = $year * 12;
		$rate = array();
        $params = array(
            'rate' => array(
    				'gjj' => array(
    						'4.0' => '12,60',
    						'4.5' => '60,360',
    				),
    				'sy' => array(
    						'moreorless' => array(1=>0.15,2=>0.1),
    						'lv' => array(
    								'6.15' => '12,36',
    								'6.40' => '48,60',
    								'6.55' => '72,360'
    						)
    				),
    		),
        );

		switch( $loan_type ) {
			case 1:

				$rate_ary = $params['rate']['gjj'];

				foreach( $rate_ary as $key => $val ) {
					$tmp_ary = explode( ',' , $val );
					if( $month >= $tmp_ary[0] && $month <= $tmp_ary[1] ) {
						$rate['rate'] = $key;
					}
				}
				break;

			case 2:

				$rate_ary = $params['rate']['sy']['lv'];

				foreach( $rate_ary as $key => $val ) {
					$tmp_ary = explode( ',' , $val );
					if( $month >= $tmp_ary[0] && $month <= $tmp_ary[1] ) {
						$rate['rate'] = $key * $rateStyle;
					}
				}
				break;

				break;
		}

		echo $callback . '(' . CJSON::encode( $rate ) . ')';

		Yii::app()->end();
    }

    /**
     * 计算房贷
     */
    public function actionCalculate()
    {
        $callback = Yii::app()->request->getQuery( 'callback' , '' );
		//贷款总价
		$total = Yii::app()->request->getPost( 'total' , Yii::app()->request->getQuery('total', 1) );
		//贷款方式	1:公积金  2:商业
		$loan_type = Yii::app()->request->getPost( 'loanStyle' , Yii::app()->request->getQuery('loanStyle', 1) );
		//还款方式  0:等额本息  1:等额本金
		$back_type = Yii::app()->request->getPost( 'back_type' , Yii::app()->request->getQuery('back_type', 1) );
		//还款月数
		$year = Yii::app()->request->getPost( 'year' , Yii::app()->request->getQuery('year', 12) );
		//利率
		$rate = Yii::app()->request->getPost( 'rate_post' , Yii::app()->request->getQuery('rate_post', '3.5') );

		$total = $total * 10000;
		$back_month = $year * 12;
		$rate = $rate / 100 / 12;

		$json_ary = array();

		switch( $back_type ) {
			case '1':		//等额本息计算方式
				//公式：a*[i*(1+i)^n]/[(1+I)^n-1] a:贷款总额,i月利率,n总月数
				$back_per_month = $total*($rate*pow($rate+1,$back_month))/(pow($rate+1,$back_month)-1);

				$json_ary['back_per_month'] = $back_per_month;
				$json_ary['month'] = $back_month;
				$json_ary['rate'] = $rate * 100 * 12;
				$json_ary['total'] = $total;
				$json_ary['back_total'] = $back_per_month * $back_month;
				$json_ary['extra'] = $json_ary['back_total'] - $json_ary['total'];

				break;
			case '2':

				$total_tmp = $total;
				$backed = 0;
				$backed_bj = 0;
				$per_month_bj = $total / $back_month;
				$i = 1;
				for( ; $i <= $back_month ; $i++ ) {
					$json_ary['every'][$i] = $total_tmp/$back_month + ($total_tmp - $backed_bj) * $rate;
					$backed_bj += $per_month_bj;
					$backed += $json_ary['every'][$i];
				}
				$json_ary['total'] = $total;
				$json_ary['month'] = $back_month;
				$json_ary['rate'] = $rate * 100 * 12;
				$json_ary['back_total'] = $backed;
				$json_ary['extra'] = $backed - $total;


				break;
			default:
				break;
		}

		echo $callback . '(' . CJSON::encode( $json_ary ) . ')';
		Yii::app()->end();
    }
}
