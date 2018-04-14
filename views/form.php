<form id="<?php echo $page ?>" action="<?= $page.".php" ?>" method="post" enctype="multipart/form-data">
    <fieldset>
        <?php form_loop($page); ?>
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
