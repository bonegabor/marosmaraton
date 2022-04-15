<div class="registration">
    <form id="<?php echo $page ?>" action="index.php" method="post" enctype="multipart/form-data">
        <fieldset>
            <div class="form-group">
                <input class="form-control" name="team_name" type="text" required placeholder="Team name*" id="team_name" autofocus>
            </div>

            <div class="form-group">
                <select id="race-select" name="race" class="form-control" required>
                    <option value="" disabled selected hidden>Race*</option>
                    <?php foreach ($races as $race) : ?>
                    <option value="<?= $race['race_id']; ?>"><?= $race['name'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <select id="category-select" name="category" class="form-control" required>
                    <option value="" disabled selected hidden>Category*</option>
                </select>
            </div>

            <div class="form-group">
                <input class="form-control" name="race_number" type="number" min="1" placeholder="Preferred race number" id="race_number">
            </div>

            <div class="team-member">
                <?php  form_loop('preregister-member-1'); ?>
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
