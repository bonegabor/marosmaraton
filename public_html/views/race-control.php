<div class="col-md-2">
    <button type="button" href="#" id="general-link" class="btn btn-info btn-block">Általános</button>
    <button type="button" href="#" id="competitors-link" class="btn btn-info btn-block">Versenyzők</button>
    <button type="button" href="#" id="categories-link" class="btn btn-info btn-block">Kategóriák</button>
    <button type="button" href="#" id="laps-link" class="btn btn-info btn-block">Futamok</button>
    <button type="button" href="#" id="results-link" class="btn btn-info btn-block">Eredmények</button>
</div>
<div class="col-md-10">
    <div id="general" class="race-tables">
    <table>
        <tr>
            <td>verseny neve</td>
            <td><?= htmlspecialchars($race['name']); ?></td>
            <td>
                <button class="btn btn-default btn-xs" onclick=" $('#change_name').show(); event.preventDefault(); ">
                    <span aria-hidden="true" class="glyphicon glyphicon-edit"></span>
                </button>
            </td>
        </tr>
        <tr>
            <td>helyszín</td>
            <td><?= htmlspecialchars($race['location']); ?></td>
            <td>
                <button class="btn btn-default btn-xs" onclick=" $('#change_name').show(); event.preventDefault(); ">
                    <span aria-hidden="true" class="glyphicon glyphicon-edit"></span>
                </button>
            </td>
        </tr>
        <tr>
            <td>időpont</td>
            <td><?= htmlspecialchars($race['date']); ?></td>
            <td>
                <button class="btn btn-default btn-xs" onclick=" $('#change_name').show(); event.preventDefault(); ">
                    <span aria-hidden="true" class="glyphicon glyphicon-edit"></span>
                </button>
            </td>
        </tr>
    </table>
    <form id="<?php echo $page ?>" action="<?= "race.php" ?>" method="post" enctype="multipart/form-data">
        <fieldset>
            <div class="form-group">
                <button class="btn btn-default" type="submit" name="closerace" value="true">
                    <span aria-hidden="true" class="glyphicon glyphicon-plus-sign"></span>
                    Verseny archiválása
                </button>
            </div>
        </fieldset>
    </form>
    </div>
    <div id="competitors" class="race-tables"></div>


    <div id="categories" class="race-tables">
        <div id="categories-table"></div>
        <button id="add-row" class="btn btn-default"><span class="glyphicon glyphicon-plus"></span>Új kategória</button>
    </div>
    <div id="laps" class="race-tables">
        <div id="laps-table"></div>
    </div>
    <div id="results" class="race-tables">
        <div id="final-table"></div>
        <button id="download-csv" class="btn btn-default"><span class="glyphicon glyphicon-download"></span>Táblázat letöltése</button>
    </div>
</div>
<div class="clearfix"></div>
