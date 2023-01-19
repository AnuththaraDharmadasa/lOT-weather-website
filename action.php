<?php

//action.php

$connect = new PDO("mysql:host=localhost;dbname=data_store", "root", "");

if(isset($_POST["action"]))
{
	if($_POST["action"] == 'fetch')
	{
		$order_column = array('Temperature', 'Record_Date');

		$main_query = "
		SELECT Temperature, Record_Date 
		FROM test_sensor_box_01
		";

		$search_query = 'WHERE Record_Date <= "'.date('Y-m-d').'" AND ';


		if(isset($_POST["search"]["value"]))
		{
			$search_query .= '(Temperature LIKE "%'.$_POST["search"]["value"].'%"  OR Record_Date LIKE "%'.$_POST["search"]["value"].'%")';
		}

		$group_by_query = " GROUP BY Record_Date ";

		$order_by_query = "";

		if(isset($_POST["order"]))
		{
			$order_by_query = 'ORDER BY '.$order_column[$_POST['order']['0']['column']].' '.$_POST['order']['0']['dir'].' ';
		}
		else
		{
			$order_by_query = 'ORDER BY Record_Date DESC ';
		}

		$limit_query = '';

		if($_POST["length"] != -1)
		{
			$limit_query = 'LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
		}

		$statement = $connect->prepare($main_query . $search_query . $group_by_query . $order_by_query);

		$statement->execute();

		$filtered_rows = $statement->rowCount();

		$statement = $connect->prepare($main_query . $group_by_query);

		$statement->execute();

		$total_rows = $statement->rowCount();

		$result = $connect->query($main_query . $search_query . $group_by_query . $order_by_query . $limit_query, PDO::FETCH_ASSOC);

		$data = array();

		foreach($result as $row)
		{
			$sub_array = array();

			$sub_array[] = $row['Temperature'];
			$sub_array[] = $row['Record_Date'];

			$data[] = $sub_array;
		}

		$output = array(
			"draw"			=>	intval($_POST["draw"]),
			"recordsTotal"	=>	$total_rows,
			"recordsFiltered" => $filtered_rows,
			"data"			=>	$data
		);

		echo json_encode($output);
	}
}

?>