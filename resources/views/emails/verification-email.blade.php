<style type="text/css">
    @import url("https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800&display=swap");

    * {
        font-family: "Open Sans" !important;
    }

    @media screen and (max-width: 960px) {
        .flex:first-of-type {
            display: block;
        }

        .flex:first-of-type p {
            text-align: center;
        }

        .package-name {

            font-size: 20px;
        }

        .name {
            font-size: 18px;
        }

    }
</style>
<div style="
            margin: 0;
            padding: 0;
            height: 100%;
">
    <header style=" background-color: #044A48;
            padding: 30px 70px;">
        <img src="{{env('SITE_URL')}}/favicon.png" title="alzuhud" alt="alzuhud" width="100" >
    </header>
    <main>
        <div class="heading" style=" padding: 40px 70px;
            background-color: #EEEEEE;">
            <span class="package-name" style="font-size: 35px; margin-bottom: 16px;">Email Verification</span><br>
            <span class="name" style=" font-size: 30px;">{{$user->full_name}}</span>
        </div>
        <div class="main-content" style="padding: 40px 70px; font-size: 14px;">

            Hi <strong>{{$user->full_name}}</strong>,
            <br>
            Welcome to <strong>{{env('SITE_URL')}}</strong>,
            <br>
            Your user account with the e-mail address <strong>{{$user->email}}</strong> has been created.
            <br>
            Please follow the link below to activate your account. The link will remain valid
            @php
            $now = new \Carbon\Carbon();
            $expireAtDate = new \Carbon\Carbon($user->email_verification_expired_at);
            @endphp
            for {{$now->diffInMinutes($expireAtDate)}} minutes.
            <br>
            <a href="{{(env('SITE_URL').'/verify-email/'.encrypt($user->id))}}">
                Click Here.
            </a>
            <br>
            You will be able to change your settings (password, language, etc.) once your account is activated.
            <br>
            If you have not requested signing up, you can just delete this email
        </div>
    </main>
    <footer style="background-color: #F3F7F3; padding: 40px 70px;">
        <div class="flex" style="display: flex !important; justify-content: space-between !important;">
            <p class="grey-text" style="color: #707070; width: 25%;"><a style="color: #707070;
            text-decoration: none; margin: 0 10px;" href="mailto:info@alzuhud.com">info@alzuhud.com</a></p>
            <p class="grey-text" style="color: #707070; width: 25%;"><a style="color: #707070;
            text-decoration: none; margin: 0 10px;" href="tel:+966122349474">+966122349474</a></p>
            <p class="grey-text" style="color: #707070; width: 25%;"><a style="color: #707070;
            text-decoration: none; margin: 0 10px;" href="tel:+966554175500">+966554175500</a></p>
            <p class="grey-text" style="color: #707070; width: 25%;">Tulsi Marg Boulevard, Saudi Arabia</p>
        </div>
        <hr style="border-color: #BDBDBD; border-width: 0.2px;">
        <div style="text-align: center; padding: 20px; font-weight: lighter; font-size: 20px;">
            Copyright (C) 2019 Alzuhud. All rights Reserved.
        </div>
    </footer>
</div>