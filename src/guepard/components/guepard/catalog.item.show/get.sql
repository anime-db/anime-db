SELECT
	i.item_id, c.category_id, i.name_ru, i.name_en,
	CONCAT(i.year_start, '-', i.year_end) AS year,
	CONCAT(c.symbolic_name, ',') AS cat_symb_name,
	CONCAT(c.name, ',') AS cat_name
FROM
	catalog_item_to_categor AS ic
LEFT JOIN
	catalog_items AS i ON i.item_id=ic.item_id
LEFT JOIN
	catalog_categories AS c ON c.category_id=ic.category_id
WHERE
	ic.item_id = 1
GROUP BY
	ic.item_id = 1;