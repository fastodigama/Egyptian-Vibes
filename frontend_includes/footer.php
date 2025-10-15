</main>

    <footer id="footer">
        <small>&copy; <?php echo date("Y"); ?> Egyptian Vibes. All rights reserved.</small>
    </footer>
    <script>
        function showSidebar() {
            const sidebar = document.querySelector('.sidebar');
            sidebar.style.display = 'flex';

        }

        function hideSidebar() {
            const sidebar = document.querySelector('.sidebar');
            sidebar.style.display = 'none';

        }
    </script>
    <script src=scripts/script.js></script>

</body>

</html>