<table class="<?php echo $class ?>">
    <?php if ($tableHead): ?>
        <thead>
        <?php  foreach ($tableHead as $headCell): ?>
              <th><?php echo $headCell ?></th>
        <?php endforeach ?>
        </thead>
    <?php endif ?>
    <tbody>
      <?php while ($row = $reader->next()): ?>
      <tr>
          <?php foreach ($row as $cell): ?>
              <td><?php echo html($cell) ?></td>
          <?php endforeach ?>
      </tr>
      <?php endwhile ?>
    </tbody>
</table>
