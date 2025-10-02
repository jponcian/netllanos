<?php

$json = array();
$txt100 = $_POST['txt100'];
$txt101 = $_POST['txt101'];
$txt102 = $_POST['txt102'];
$txt103 = $_POST['txt103'];
$txt104 = $_POST['txt104'];
$txt105 = $_POST['txt105'];
$txt106 = $_POST['txt106'];
$txt107 = $_POST['txt107'];
$txt108 = $_POST['txt108'];
$txtconforme = $_POST['txtconforme'];

//ARTICULO 100
$art100_1 = substr($txt100, 0, 1);
$art100_2 = substr($txt100, 1, 1);
$art100_3 = substr($txt100, 2, 1);
$art100_4 = substr($txt100, 3, 1);

//ARTICULO 101
$art101_1 = substr($txt101, 0, 1);
$art101_2 = substr($txt101, 1, 1);
$art101_3 = substr($txt101, 2, 1);
$art101_4 = substr($txt101, 3, 1);
$art101_5 = substr($txt101, 4, 1);
$art101_6 = substr($txt101, 5, 1);
$art101_7 = substr($txt101, 6, 1);
$art101_8 = substr($txt101, 7, 1);
$art101_9 = substr($txt101, 8, 1);
$art101_10 = substr($txt101, 9, 1);
$art101_11 = substr($txt101, 10, 1);

//ARTICULO 102
$art102_1 = substr($txt102, 0, 1);
$art102_2 = substr($txt102, 1, 1);
$art102_3 = substr($txt102, 2, 1);
$art102_4 = substr($txt102, 3, 1);
$art102_5 = substr($txt102, 4, 1);
$art102_6 = substr($txt102, 5, 1);
$art102_7 = substr($txt102, 6, 1);
$art102_8 = substr($txt102, 7, 1);

//ARTICULO 103
$art103_1 = substr($txt103, 0, 1);
$art103_2 = substr($txt103, 1, 1);
$art103_3 = substr($txt103, 2, 1);
$art103_4 = substr($txt103, 3, 1);
$art103_5 = substr($txt103, 4, 1);
$art103_6 = substr($txt103, 5, 1);
$art103_7 = substr($txt103, 6, 1);

//ARTICULO 104
$art104_1 = substr($txt104, 0, 1);
$art104_2 = substr($txt104, 1, 1);
$art104_3 = substr($txt104, 2, 1);
$art104_4 = substr($txt104, 3, 1);
$art104_5 = substr($txt104, 4, 1);
$art104_6 = substr($txt104, 5, 1);
$art104_7 = substr($txt104, 6, 1);
$art104_8 = substr($txt104, 7, 1);
$art104_9 = substr($txt104, 8, 1);
$art104_10 = substr($txt104, 9, 1);
$art104_11 = substr($txt104, 10, 1);
$art104_12 = substr($txt104, 11, 1);
$art104_13 = substr($txt104, 12, 1);
$art104_14 = substr($txt104, 13, 1);

//ARTICULO 105
$art105_1 = substr($txt105, 0, 1);
$art105_2 = substr($txt105, 1, 1);
$art105_3 = substr($txt105, 2, 1);
$art105_4 = substr($txt105, 3, 1);
$art105_5 = substr($txt105, 4, 1);

//ARTICULO 106
$art106_1 = substr($txt106, 0, 1);
$art106_2 = substr($txt106, 1, 1);
$art106_3 = substr($txt106, 2, 1);

//ARTICULO 107
$art107_1 = substr($txt107, 0, 1);
$art107_2 = substr($txt107, 1, 1);
$art107_3 = substr($txt107, 2, 1);
$art107_4 = substr($txt107, 3, 1);

//ARTICULO 108
$art_108 = substr($txt108, 0, 1);;

//ARTICULO conforme
$art_conforme = substr($txtconforme, 0, 1);;

$json = array('art100_1' => $art100_1,
				'art100_2' => $art100_2,
				'art100_3' => $art100_3,
				'art100_4' => $art100_4,
				'art101_1' => $art101_1,
				'art101_2' => $art101_2,
				'art101_3' => $art101_3,
				'art101_4' => $art101_4,
				'art101_5' => $art101_5,
				'art101_6' => $art101_6,
				'art101_7' => $art101_7,
				'art101_8' => $art101_8,
				'art101_9' => $art101_9,
				'art101_10' => $art101_10,
				'art101_11' => $art101_11,
				'art102_1' => $art102_1,
				'art102_2' => $art102_2,
				'art102_3' => $art102_3,
				'art102_4' => $art102_4,
				'art102_5' => $art102_5,
				'art102_6' => $art102_6,
				'art102_7' => $art102_7,
				'art102_8' => $art102_8,
				'art103_1' => $art103_1,
				'art103_2' => $art103_2,
				'art103_3' => $art103_3,
				'art103_4' => $art103_4,
				'art103_5' => $art103_5,
				'art103_6' => $art103_6,
				'art103_7' => $art103_7,
				'art104_1' => $art104_1,
				'art104_2' => $art104_2,
				'art104_3' => $art104_3,
				'art104_4' => $art104_4,
				'art104_5' => $art104_5,
				'art104_6' => $art104_6,
				'art104_7' => $art104_7,
				'art104_8' => $art104_8,
				'art104_9' => $art104_9,
				'art104_10' => $art104_10,
				'art104_11' => $art104_11,
				'art104_12' => $art104_12,
				'art104_13' => $art104_13,
				'art104_14' => $art104_14,
				'art105_1' => $art105_1,
				'art105_2' => $art105_2,
				'art105_3' => $art105_3,
				'art105_4' => $art105_4,
				'art105_5' => $art105_5,
				'art106_1' => $art106_1,
				'art106_2' => $art106_2,
				'art106_3' => $art106_3,
				'art107_1' => $art107_1,
				'art107_2' => $art107_2,
				'art107_3' => $art107_3,
				'art107_4' => $art107_4,
				'art_108' => $art_108,
				'art_conforme' => $conforme
	);

echo json_encode($json);

?>