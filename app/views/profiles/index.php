<table class="table">
<thead>
<tr>
    <th>#</th>
    <th>Nombre</th>
    <th>Apellidos</th>
    <th>Email</th>
</tr>
</thead>
<tbody>

<?php if (isset($profiles)) : ?>
    <?php foreach ($profiles as $k => $profile) : ?>

    <tr>
        <th scope="row"><?= $k+1 ?></th>
        <td><a href="/profiles/<?= $profile['id'] ?>"><?= $profile['nombres'] ?></a></td>
        <td><?= $profile['apellidos'] ?></td>
        <td><?= $profile['email'] ?></td>
    </tr>

    <?php endforeach; ?>
    </tbody>
    </table>

    <?php if ( isset($pages) && $pages > 1 ) : ?>
    <nav>
        <ul class="pagination">
            <li>
                <a href="<?= ( $page > 1) ? '?page=' . ($page - 1) : '#' ?>" aria-label="Previous">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>
            <?php
            while ( $start <= $length ) : ?>
                <?php if ($start == $page ) : ?>
                    <li class="active"><a href="?page=<?= $start ?>"><?= $start ?></a></li>
                <?php else : ?>
                    <li><a href="?page=<?= $start ?>"><?= $start ?></a></li>
                <?php endif; ?>
            <?php
            $start++;
            endwhile; ?>

            <li>
                <a href="<?= ( $page < $pages) ? '?page=' . ($page + 1) : '#' ?>" aria-label="Next">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
        </ul>
    </nav>
    <?php endif; ?>
<?php endif; ?>
