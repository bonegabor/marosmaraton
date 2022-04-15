<div class="finish">
    <form id="finish-form" action="#" method="get" enctype="multipart/form-data">
        <fieldset>
            <div class="form-group">
                <input autocomplete="off" autofocus id="race_number" class="form-control" name="race_number" placeholder="Rajtszám" type="number" step="any" required/>
            </div>
            <div class="form-group">
                <button id="finish-button" class="btn btn-default" type="submit">
                    <span aria-hidden="true"><img src="img/finish_flag.png" style="height: 20px;"/></span>
                </button>
            </div>
        </fieldset>
    </form>
    <div id="alert" class="alert alert-danger"></div>
<div id="finish-table"></div>
<div style="margin-top: 50px">
<a href="afuncs.php?process=race&action=finish-lap" id="finish-lap" class="btn btn-default btn-danger" role="button">Futam lezárása</a>
</div>
</div>
