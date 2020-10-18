$(function() {
	$("#select-and-commit-btn").click(function(e) {
		$("#select-file").click();
		e.preventDefault();
		e.stopPropagation();
	});

	$("#select-file").change(function(e) {
		$("#load-file-form").submit();
	});
});

