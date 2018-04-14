<form action="register.php" method="post">
    <fieldset>
        <div class="form-group">
            <input autocomplete="off" autofocus class="form-control" name="name" placeholder="Name" type="text"/>
        </div>
        <div class="form-group">
            <input autocomplete="off" autofocus class="form-control" name="username" placeholder="Username" type="text"/>
        </div>
        <div class="form-group">
            <input autocomplete="off" autofocus class="form-control" name="email" placeholder="user@example.com" type="email"/>
        </div>
        <div class="form-group">
            <input class="form-control" name="password" placeholder="Password" type="password"/>
        </div>
        <div class="form-group">
            <input class="form-control" name="confirmation" placeholder="Confirm password" type="password"/>
        </div>
<!--        <div class="g-recaptcha" data-sitekey="6LdMthIUAAAAAP34iG_8J4QVcr1Oozqg5304onlU"></div> -->
        <div class="form-group">
            <div class="g-recaptcha" data-sitekey="6LfhbxMTAAAAANBgsv6lJ4C6e4upVdYzsBVwmAWT" style="display: inline-block; "></div>
        </div>
        <div class="form-group">
            <button class="btn btn-default" type="submit">
                <span aria-hidden="true" class="glyphicon glyphicon-log-in"></span>
                Register
            </button>
        </div>
    </fieldset>
</form>
<div>
    or <a href="login.php">login</a>
</div>
