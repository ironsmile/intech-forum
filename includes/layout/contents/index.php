<table summary="Forums">
	<caption>Forums</caption>
	<thead>
		<tr>
			<th>Forum name</th>
			<th>Total topics</th>
			<th>Total posts</th>
			<th>Last post</th>
		</tr>
	</thead>
	<tbody>
<?php
	foreach($requestData['forums'] as $forum) {
		echo "<tr>";
		echo "<td>{$forum->name}</td>";
		echo "<td>" . $forum->getNumberOfTopics() . "</td>";
		echo "<td>" . $forum->getNumberOfPosts() . "</td>";
		echo "<td>" . $forum->getLastPost() . "</td>";
		echo "</tr>";
	} 
?>
	</tbody>
</table>