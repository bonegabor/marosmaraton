<?php if ($_SESSION['lap_id'] == null) : ?>
<form id="start-lap" action="start.php" method="post" enctype="multipart/form-data">
    <button type="submit" href="#" id="new-lap" value="new" class="btn btn-success btn-lg">Futam indítás</button>
</form>
<?php else : ?>
    <?php if($_SESSION['race_start'] == 'category') : ?>
            <?= htmlspecialchars($_SESSION['lap_no']).". futam"; ?>
            </br>
            <table id="start" class="table table-hover">
            <?php foreach ($categories as $cat) : ?>
                <tr>
                    <td>
                        <button type="button" href="#" id="<?= htmlspecialchars($cat['cat_id']); ?>" class="btn btn-success btn-block start-button" <?= ($cat['start_time'] == null ? '' : 'disabled'); ?>><?= htmlspecialchars($cat['shortname']); ?></button>
                    </td>
                    <td id="starttime-<?= htmlspecialchars($cat['cat_id']); ?>">
                        <?php if ($cat['start_time'] != null)
                                print($cat['start_time']);
                              else 
                                echo '00:00:00'; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </table>

    <?php elseif ($_SESSION['race_start'] == 'team') : ?>
    <?= htmlspecialchars($_SESSION['lap_no']).". futam"; ?>
    </br>
    <script>
    $(document).ready(function () {
    });
    </script>
    <form id="start-finish-form" action="#" method="get" enctype="multipart/form-data">
        <fieldset>
            <div class="form-group">
                <input autocomplete="off" autofocus id="race_number" class="form-control" name="race_number" placeholder="Rajtszám" type="number" step="any" required/>
            </div>
            <div class="start-finish">
                <button id="start-button" class="btn btn-default btn-success" value="start">
                    <span aria-hidden="true">START</span>
                </button>
                <button id="finish-button" class="btn btn-default btn-danger" value="finish">
                    <span aria-hidden="true">FINISH</span>
                </button>
            </div>
        </fieldset>
    </form>
    <div id="alert" class="alert alert-danger"></div>
    <div class="stopwatch"></div>
    <ul class="results"></ul>
    <div id="finish-table"></div>
    <div style="margin-top: 50px">
    <a href="afuncs.php?process=race&action=finish-lap" id="finish-lap" class="btn btn-default btn-danger" role="button">Futam lezárása</a>


    <?php endif; ?>
<?php endif; ?>
