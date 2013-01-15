<!doctype html>
<html>
	<head>
		<title>GMHost - Free File Hosting for Game Maker-related files</title>
		<link rel="stylesheet" href="/static/style.css">
	</head>
	<body>
		<div id="main">
			<h1>GMHost</h1>
			<p><strong>Free hosting for your Game Maker-related files.</strong> Rules: 1. Only Game Maker-related files, 2. Don't get me abuse-mail, 3. Don't upload malicious files, 4. Controversy is fine, blatantly illegal things that you could find anywhere else are not, 5. Don't be a dick.</p>
			<p><em>Note that while technically all should go fine, this is still a new and <strong>experimental</strong> service. Things may go wrong. Bug reports
			and abuse complaints can be directed at gmhost@cryto.net.</em></p>
			
			<div id="newdir">
				<form action="/newdir" method="post">
					<strong>Directory name:</strong> {%?current-dir} <input type="text" name="name">
					<input type="hidden" name="currentdir" value="{%?current-dir}">
					<button type="submit" name="submit" value="submit">Create new directory</button>
				</form>
			</div>
			<div id="upload">
				<form action="/upload" method="post" enctype="multipart/form-data">
					<input type="file" name="file"> (100MB max.)
					<input type="hidden" name="currentdir" value="{%?current-dir}">
					<button type="submit" name="submit" value="submit">Upload in this directory</button>
				</form>
			</div>
			
			<table>
				<tr>
					<th></th>
					<th>Name</th>
					<th>Size</th>
				</tr>
				{%if current-dir != "/"}
					<tr>
						<td class="img"><img src="/static/arrow.png"></td>
						<th><a href="{%?parent-url}">Back to parent directory</a></th>
						<td></td>
					</tr>
				{%/if}
				{%foreach directory in directories}
					<tr>
						<td class="img"><img src="/static/folder.png"></td>
						<th><a href="{%?directory[url]}">{%?directory[name]}</a></th>
						<td></td>
					</tr>
				{%/foreach}
				{%foreach file in files}
					<tr>
						<td class="img"><img src="/static/file.png"></td>
						<th><a href="{%?file[url]}">{%?file[name]}</a></th>
						<td>{%?file[size]} bytes</td>
					</tr>
				{%/foreach}
			</table>
		</div>
	</body>
</html>
