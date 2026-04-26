    </div>
</div>

<script>
    // Add active class to current nav item
    $(document).ready(function() {
        var currentUrl = window.location.pathname.split('/').pop();
        $('.nav-link').each(function() {
            var linkUrl = $(this).attr('href').split('/').pop();
            if (currentUrl === linkUrl) {
                $(this).addClass('active');
            }
        });
    });
</script>
</body>
</html>
