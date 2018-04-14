<form id="<?php echo $page ?>" action="<?= "race.php" ?>" method="post" enctype="multipart/form-data">
    <fieldset>
        <div class="form-group">
            <select id="race" name="race" class="form-control" >';
                <option value="" disabled selected hidden>válassz versenyt...</option>
<?php foreach ($races as $race) : ?>
    <?php if ($race['closed'] == true) : ?>
                 <option value="<?= htmlspecialchars($race['race_id']); ?>" style="background-color: #eeeeee;"><?= htmlspecialchars($race['name']); ?></option>';
    <?php else : ?>
                 <option value="<?= htmlspecialchars($race['race_id']); ?>" style="background-color: #ffffff;"><?= htmlspecialchars($race['name']); ?></option>';
    <?php endif; ?>
<?php endforeach; ?>
            </select>
        </div>
        
        <div class="form-group">
            <button class="btn btn-default" type="submit">
                <span aria-hidden="true" class="glyphicon glyphicon-plus-sign"></span>
                <?= $title ?>
            </button>
        </div>
    </fieldset>
</form>
<div class="modal fade" id="basicModal" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content"></div>
    </div>
</div>
