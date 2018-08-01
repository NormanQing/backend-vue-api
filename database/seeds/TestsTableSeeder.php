<?php

use Illuminate\Database\Seeder;

class TestsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		$data = [];
		$firstName = [
		'王','李' ,'张' ,'刘', '陈', '杨', '赵', '黄', '周', '吴 ',
		'徐', '孙', '胡', '朱', '高', '林', '何', '郭', '马', '罗',
		'程', '曹', '袁', '邓', '许', '傅', '沈', '曾', '彭', '吕',
		'苏', '卢', '蒋', '蔡', '贾', '丁', '魏', '薛', '叶', '阎',
		'余', '潘', '杜', '戴', '夏', '锺', '汪', '田', '任', '姜',
		'范', '方', '石', '姚', '谭', '廖', '邹', '熊', '金', '陆',
		'郝', '孔', '白', '崔', '康', '毛', '邱', '秦', '江', '史',
		'顾', '侯', '邵', '孟', '龙', '万', '段', '雷', '钱', '汤',
		'尹', '黎', '易', '常', '武', '乔', '贺', '赖', '龚', '文'];
		$lastName = [
			'伟', '芳', '娜', '秀英', '敏', '静', '丽', '强', '磊', '军',
			'洋', '勇', '艳', '杰', '娟', '涛', '明', '超', '秀兰', '霞',
			'平', '刚', '桂英'];
		$sex = ['f','m'];
		$hoby = ['篮球','足球','排球','游泳'];
		$status = [0,1];
		$str = [1,2,3,4,5,6,7,8,9,0,'a','b','c','d','e'.'f','g','h','i','j','k','l','m','n','o','p','q'];
		$str2 = ['a','b','c','d','e'.'f','g','h','i','j','k','l','m','n','o','p','q'];
		$lastPre = ['com','cn','net','org'];
		for($i=1;$i<=40;$i++){
			$itemCname = $firstName[array_rand($firstName,1)].$firstName[array_rand($lastName,1)];
			$emailRandKey = array_rand($str,rand(2,count($str)));
			$userEmail = '';
			foreach($emailRandKey as $val){
				$userEmail .= $str[$val];
			}
			$doMain = '';
			foreach(array_rand($str2,5) as $domain){
				$doMain .= $str2[$domain];
			}
			$itemEmail = $userEmail.'@'.$doMain.'.'.$lastPre[array_rand($lastPre,1)];
			
			$itemSex = $sex[array_rand($sex,1)];
			
			$randNum = rand(1,count($hoby));
			$itemHobyKey = array_rand($hoby,$randNum);
			$itemHoby = '';
			if(is_array($itemHobyKey)){
				foreach($itemHobyKey as $v){
					$itemHoby .= $hoby[$v].',';
				}
			}else{
				$itemHoby .= $hoby[$itemHobyKey];
			}
			
			$itemStatus = array_rand($status,1);
			$now = date('Y-m-d:H:i:s');
			$data[] = [
				'user' => $itemCname,
				'email' => $itemEmail,
				'sex' => $itemSex,
				'hoby' => trim($itemHoby,','),
				'status' => $itemStatus,
				'created_at' => $now,
				'updated_at' => $now
			];
		}
		// var_dump($data);
		\Illuminate\Support\Facades\DB::table('tests')->insert($data);
    }
}
