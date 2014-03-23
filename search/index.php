<?php

require_once("../inc/config.php");

$search_term = "";
if(isset($_GET["s"])){
	$search_term = trim($_GET["s"]);
	if($search_term != ""){
		require_once(ROOT_PATH . "inc/products.php");
		$products = get_products_search($search_term);
	}
}

$pageTitle = "Search";
$section = "search";
include(ROOT_PATH . "inc/header.php"); 

?>

<div class="section shirts search page">
	<div class="wrapper">
		<h1>Search</h1>

		<form method="get" action="./">
			<input type="text" name="s" value="<?php if($search_term!=""){echo $search_term;}?>">
			<input type="submit" value="Go">
		</form>

		<?php

		if($search_term != ""){
			if(!empty($products)){
				echo '<ul class="products">';
				foreach($products as $product){
					include(ROOT_PATH . "inc/partial-list-view.html.php");
					
				}
				echo '</ul>';
			}else{
				echo '<p>No products were found matching that search term</p>';
			}
		}

		?>
	</div>
</div>

<?php include(ROOT_PATH . "inc/footer.php"); ?>