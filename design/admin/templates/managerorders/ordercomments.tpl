<table cellspacing="0" cellpadding="0" border="0" width="100%" class="list">
	<tbody>
		<tr>
			<th>Date</th>
			<th>User</th>
			<th>Comment</th>
		</tr>
		{foreach $comments as $comment}
			<tr>
				<td>{$comment.date}</td>
				<td>{$comment.user}</td>
				<td>{$comment.comment_text}</td>
			</tr>
		{/foreach}
	</tbody>
</table>
