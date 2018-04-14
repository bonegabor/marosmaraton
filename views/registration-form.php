<div class="registration">
    <form id="<?php echo $page ?>" action="<?= $page.".php" ?>" method="post" enctype="multipart/form-data">
        <fieldset>
            <div class="form-group">
                <input class="form-control" name="team_name" type="text" required placeholder="Csapatnév*" id="team_name" autofocus>
            </div>
            <div class="form-group">
                <input class="form-control" name="race_number" type="number" min="1" required placeholder="Rajtszám*" id="race_number">
            </div>
            <div class="form-group">
                <select id="category-select" name="category" class="form-control" required>
                    <option value="" disabled selected hidden>Kategória*</option>
                    <?php foreach ($categories as $category) :?>
                        <option value="<?= $category['cat_id']; ?>"><?= htmlspecialchars($category['shortname']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
                <div class="team-member">
                    <?php form_loop('register-member-1'); ?>
                </div>
            <div id="team-members">
            </div>
            <div class="form-group">
                <button id="registration-button" class="btn btn-default" type="submit">
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
</div>
