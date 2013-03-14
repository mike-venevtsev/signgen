<?php

use devcookies\SignatureGenerator;

class SignatureGeneratorTest extends PHPUnit_Framework_TestCase
{
	protected $salt = "some salt";
	
	/**
	 * @test
	 * @dataProvider signParamsProvider
	 */
	public function paramSet(array $params, $resultSign)
	{
		$signgen = new SignatureGenerator($this->salt);
		
		$this->assertEquals($resultSign, $signgen->assemble($params));
	}
	
	public function signParamsProvider()
	{
		return array(
			array( // 
				array(
					'c_param' => 'c',
					'a3_param' => 'a3',
					'b3_param' => 'b3',
					'a2_param' => true,
					'b2_param' => false,
					'a1_param' => 1,
					'b1_param' => 2,
				),
				'4c6f5e5471953977779fee0e1de698859883800f'
			),
			array(
				array(
					'a_param' => 'value 1',
					'd_param' => 'value_3',
					'c_param' => array(
						'c_sub_param' => 'sub value c3',
						'a_sub_param' => 'sub value a1',
						'b_sub_param' => 'sub value b2',
						'a_sub_param' => true,
						'b_sub_param' => false,
					),
					'b_param' => 'value_2',
				),
				'79d6286011ba043a0c041c91e75d96c060b81357'
			),
			array(
				array(
					'c_param' => array(
						'c_sub_param' => array(
							'a_sub_sub_param' => 'value 3',
						),
						'a_sub_param' => 'value 2',
					),
					'a_param' => 'value 1',
				),
				'804ea804e9a784a49585d2dbb1ed5d310479099f'
			)
		);
	}
}