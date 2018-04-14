<div>
    <h4>Felhasználónév</h4>
    <?= htmlspecialchars($user["username"]) ?>
</div>
<hr>
<div>
    <h4>Név</h4>
    <?= htmlspecialchars($user["name"]) ?>
    <button class="btn btn-default btn-xs" onclick=" $('#change_name').show(); event.preventDefault(); ">
        <span aria-hidden="true" class="glyphicon glyphicon-edit"></span>
    </button>
    <form id="change_name" class="profile-control" action="profile.php" method="post">
        <fieldset>
            <div class="form-group">
                <input class="form-control" name="new_name" placeholder="Új név" type="text"/>
            </div>
            <div class="form-group">
                <button class="btn btn-default" name="submit" type="submit" value="change_name">
                    <span aria-hidden="true" class="glyphicon glyphicon-refresh"></span>
                    Név csere
                </button>
            </div>
        </fieldset>
    </form>
</div>
<hr>
<div>
    <h4>Email cím</h4>
    <?= htmlspecialchars($user["email"]) ?>
    <button class="btn btn-default btn-xs" onclick=" $('#change_email').show(); event.preventDefault(); ">
        <span aria-hidden="true" class="glyphicon glyphicon-edit"></span>
    </button>
    <form id="change_email" class="profile-control" action="profile.php" method="post">
        <fieldset>
            <div class="form-group">
                <input class="form-control" name="new_email" placeholder="Új email cím" type="text"/>
            </div>
            <div class="form-group">
                <button class="btn btn-default" name="submit" type="submit" value="change_email">
                    <span aria-hidden="true" class="glyphicon glyphicon-refresh"></span>
                    Email csere
                </button>
            </div>
        </fieldset>
    </form>
</div>
<hr>
<div>
    <h4>Jelszó csere</h4>
    <button class="btn btn-default btn-xs" onclick=" $('#change_pwd').show(); event.preventDefault(); ">
        <span aria-hidden="true" class="glyphicon glyphicon-edit"></span>
    </button>
    <form id="change_pwd" class="profile-control" action="profile.php" method="post">
        <fieldset>
            <div class="form-group">
                <input class="form-control" name="old_password" placeholder="Régi jelszó" type="password"/>
            </div>
            <div class="form-group">
                <input class="form-control" name="new_password" placeholder="Új jelszó" type="password"/>
            </div>
            <div class="form-group">
                <input class="form-control" name="confirmation" placeholder="Jelszó ismét" type="password"/>
            </div>
            <div class="form-group">
                <button class="btn btn-default" name="submit" type="submit" value="change_pwd">
                    <span aria-hidden="true" class="glyphicon glyphicon-refresh"></span>
                    Jelszó csere
                </button>
            </div>
        </fieldset>
    </form>
</div>
