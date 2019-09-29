<table class="<?= $class ?>">
    <?php if ($tableHead): ?>
        <thead>
        <?php  foreach ($tableHead as $headCell): ?>
              <th><?= $headCell ?></th>
        <?php endforeach ?>
        </thead>
    <?php endif ?>
    <tbody>
      <?php while ($row = $reader->next()): ?>
      <tr>
          <?php foreach ($row as $key => $cell): ?>
              <td><?= $cell ?></td>
          <?php endforeach ?>
      </tr> 
      <?php endwhile ?>
    </tbody>
</table>