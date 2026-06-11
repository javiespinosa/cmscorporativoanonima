<footer class="bg-dark text-white py-4">

<div class="container">

<div class="row">

<div class="col-md-6">

<?= htmlspecialchars($config['empresa']) ?>

</div>

<div class="col-md-6 text-end">

<?= htmlspecialchars($config['telefono']) ?>

</div>

</div>

</div>

</footer>

<a
class="btn btn-success whatsapp"
target="_blank"
href="https://wa.me/52<?= $config['whatsapp'] ?>">

<i class="fab fa-whatsapp fa-2x"></i>

</a>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>