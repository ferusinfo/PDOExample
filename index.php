<?php
// Dead Simple example of a Categories Tree
include_once('models/categories.php');

$categories = new Categories();
$object = $categories->fetchCategoriesAndTree();

// Print main categories and go down in the tree
foreach ($object->main_cats as $main_category_id)
{
	print_categories($object, $main_category_id);
}

function print_categories($object, $category_id)
{
	$current_tree = $object->tree[$category_id];

	$current_category = $object->categories[$category_id];
	$string = '';
	for ($i=0; $i<=$current_category->category_level; $i++)
	{
		// just a categories separator, can be anything basically, */()
		$string .= "-";
	}

	$string .= " " . $current_category->category_name . "</br>";
	echo $string;

	if (!empty($current_tree))
	{
		foreach ($current_tree as $sub_cat)
		{
			print_categories($object, $sub_cat);
		}
	}
}
?>
