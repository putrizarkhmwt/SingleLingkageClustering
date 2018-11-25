<!DOCTYPE html> 
<html lang="en"> 
<head> 
    <meta charset="UTF-8"> 
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
    <meta http-equiv="X-UA-Compatible" content="ie=edge"> 
    <title>Clustering</title> 
</head> 
<body> 
	<h3>Clustering</h3>
	<form action="" method="post">
	Masukkan Jumlah Cluster yang Diinginkan:
		<table>
			<tr>
				<td><input type="textfield" name="k"/></td>
				<td><input type="submit" name="submit" value="OK"/></td>
			</tr>
		</table>
	</form>
	<a href="index.php">Kembali Ke Beranda</a>
	<div id="chartContainer" style="height: 540px; width: 100%;"></div> 
    <script src="canvasjs.min.js"></script> 
</body> 
</html> 
<?php 
	if(isset($_POST['k'])) { 
		$k = $_POST['k'];
		$txt_file1    = file_get_contents('dataset_ruspini.txt');
		$rows1        = explode("\n", $txt_file1); // Memisahkan Item Data dariPemisah enter

		//proses mengambil titik
		$i=0;
		$N = count($rows1);
		$n = $N;
		$jml_data = $N;
		$index_cluster = 0;
		
		foreach($rows1 as $row1 => $data_test)
		{
			$row_data_test= explode('|', $data_test);// Memisahkan Item Data dariPemisah |
			$ket_cluster[$i] = $i;
			$titikx_data[$i] = $row_data_test[0];
			$titiky_data[$i] = $row_data_test[1];
			$i++;
		}
		
		//proses menghitung jarak
		for($i=0; $i<$jml_data; $i++){
			for($j=0; $j<$jml_data; $j++){
				$jarak[$i][$j] = sqrt(pow((floatval($titikx_data[$i])- floatval($titikx_data[$j])),2) + pow((floatval($titiky_data[$i])- floatval($titiky_data[$j])),2));
			}
		}
		
		//proses mencari jarak dan merubah cluster
		while($n>=$k){
			for($i=0; $i<$jml_data; $i++){
				for($j=0; $j<$jml_data; $j++){
					//mencari jarak paling kecil pada satu baris
					if(!isset($min[$i]['jarak'])){
						$min[$i]['jarak'] = $jarak[$i][$j];
						$min[$i]['data1'] = $i;
						$min[$i]['data2'] = $j;
					}else{
						if($jarak[$i][$j] < $min[$i]['jarak']){
							if($jarak[$i][$j]!=0){
								$min[$i]['jarak'] = $jarak[$i][$j];
								$min[$i]['data1'] = $i;
								$min[$i]['data2'] = $j;	
							}
						}else{
							if($min[$i]['jarak']==0){
								$min[$i]['jarak'] = $jarak[$i][$j];
								$min[$i]['data1'] = $i;
								$min[$i]['data2'] = $j;
							}
						}
					}
				}
				//membandingkan jarak paling minim pada tabel
				if(!isset($min_bgt['jarak'])){
					$min_bgt['jarak'] = $min[$i]['jarak'];
					$min_bgt['data1'] = $min[$i]['data1'];
					$min_bgt['data2'] = $min[$i]['data2'];
				}else{
					if($min[$i]['jarak'] < $min_bgt['jarak']){
						$min_bgt['jarak'] = $min[$i]['jarak'];
						$min_bgt['data1'] = $min[$i]['data1'];
						$min_bgt['data2'] = $min[$i]['data2'];
					}
				}
			}
			
			//menentukan cluster baru dengan mengikuti cluster terkecil dari dua data terpilih
			$cluster_baru = min($min_bgt['data1'],$min_bgt['data2']);
			//mengganti jarak terkecil dengan nilai paling besar
			$jarak[$min_bgt['data1']][$min_bgt['data2']] = 1000;
			$jarak[$min_bgt['data2']][$min_bgt['data1']] = 1000;
			
			//mengganti cluster
			$cluster_ganti = $ket_cluster[$cluster_baru];
			for($i=0; $i<$jml_data; $i++){
				if($ket_cluster[$i] == $cluster_ganti){
					$ket_cluster[$i] = $cluster_baru;
				}
			}
			for($i=0; $i<$jml_data; $i++){
				if($ket_cluster[$i] == $min_bgt['data1']){
					$ket_cluster[$i] = $cluster_baru;
				}else if($ket_cluster[$i] == $min_bgt['data2']){
					$ket_cluster[$i] = $cluster_baru;
				}
			}

			$ket_cluster[$min_bgt['data1']] = $cluster_baru;
			$ket_cluster[$min_bgt['data2']] = $cluster_baru;
			
			//mencari data yang unik dari data var cluster
			$cluster = array_unique($ket_cluster);
			//mengurutkan index dari data unik
			$cluster = array_values($cluster);
		
			for($n=0; $n<count($cluster); $n++){
				for($m=0; $m<$jml_data; $m++){
					if($cluster[$n] == $ket_cluster[$m]){

					}
				}
			}
			
			//mengosongkan nilai minimal baris untuk iterasi selanjutnya
			for($i=0; $i<$jml_data; $i++){
				$min[$i]['jarak']= NULL;
			}
			$min_bgt['jarak'] = NULL;
			$n--;
		}	
		$i=0;
		foreach($rows1 as $row1 => $data_test)
		{
			$row_data_test= explode('|', $data_test);// Memisahkan Item Data dariPemisah |
			$dataPoints[$i] = array("x"=>$row_data_test[0] , "y"=>$row_data_test[1], "cluster"=>$ket_cluster[$i]);
			$label[$i] = $dataPoints[$i]['cluster'];
			$i++;
		}
		//menyimpan cluster yang beda
		$label = array_unique($label);
		$label = array_values($label);
		
		//menyimpan data dengan cluster tertentu
		for($i=0; $i<count($label); $i++){
			$l = 0;
			for($j=0; $j<$jml_data; $j++){
				if($dataPoints[$j]['cluster'] == $label[$i]){
					$class[$i][$l]['x'] = $dataPoints[$j]['x'];
					$class[$i][$l]['y'] = $dataPoints[$j]['y'];
					$class[$i][$l]['cluster'] = $dataPoints[$j]['cluster'];
					$l++;
				}
			}
		}
		
		$sumjarak = 0;
		for($i=0; $i<count($label); $i++){
			
			//menghitung rata rata titik centroid cluster
			$sumtitikx = 0;
			$sumtitiky = 0;
			$sumdata = 0;
			for($j=0; $j<$jml_data; $j++){
				if($dataPoints[$j]['cluster'] == $label[$i]){
					$sumtitikx +=  $dataPoints[$j]['x'];
					$sumtitiky +=  $dataPoints[$j]['y'];
					$sumdata++;
				}
			}
			$centroid[$i]['x'] = $sumtitikx / $sumdata;
			$centroid[$i]['y'] = $sumtitiky / $sumdata;
			
			//menghitung jarak tiap data
			for($j=0; $j<$jml_data; $j++){
				if($dataPoints[$j]['cluster'] == $label[$i]){
					$jarakcentroid[$i][$j] = sqrt(pow((floatval($centroid[$i]['x'])- floatval($dataPoints[$j]['x'])),2) + pow((floatval($centroid[$i]['y'])- floatval($dataPoints[$j]['y'])),2));
					$sumjarak += $jarakcentroid[$i][$j];
				}
			}
		}
		
		$error_analys = $sumjarak / $jml_data;
		
		echo "Hasil Error Analys Dengan Metode Sum of Squared Error Adalah = ".$error_analys;
?>
<script>
window.onload = function () {
 
var chart = new CanvasJS.Chart("chartContainer", {
	title: {
		text: "Hasil Visualisasi Cluster"
	},
	axisX: {
		title: "X",
	},
	axisY: {
		title: "Y",
	},
	data: [

	<?php for ($i=0; $i < count($label); $i++) { 
		
	?>
	{	
		type: "scatter", 
		markerType: "circle", 
		markerSize: 10, 
		toolTipContent: "y: {y}<br>x: {x}<br>Label: {cluster}", 
		dataPoints: <?php echo json_encode($class[$i], JSON_NUMERIC_CHECK); ?>
	},

	<?php } ?>

	]
});
chart.render();
 
}
</script>
<?php 	
} ?>