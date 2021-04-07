<!DOCTYPE html>
<html>
<head>
    <title>Hi</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        body {
            font-family: 'Open Sans', sans-serif;
        }
    </style>
</head>
<body>
<div style="display: block; height: auto;">
    <h1 style="width: 100%; color: #525252; font-size: 12px; height: auto;">
        information
    </h1>
    <div style="width: 100%; height: auto; display: block;">
        <div style="float: left; width: 33.33333333%; height: auto; text-align: left;">
            <h4 style="font-family: 'Open Sans', sans-serif; text-transform: capitalize; color: #a7a7a7; font-size: 9px; height: 10px;">
                Title
            </h4>
            <h3 style="font-family: 'Open Sans', sans-serif; color: #525252; font-size: 10px; height: 10px; margin-top: -8px;">
                {{$booking->package_id->name}}
            </h3>
        </div>
        <div style="float: left; width: 33.33333333%; height: auto; text-align: left;">
            <h4 style="text-transform: capitalize; color: #a7a7a7; font-size: 9px;">
                From
            </h4>
            <h3 style="color: #525252; font-size: 10px; margin-top: -8px;">
                @php $from = new \Illuminate\Support\Carbon($booking->package_id->start_date);  @endphp
                {{ $from->format('Y.m.d') }}
            </h3>
        </div>
        <div style="float: left; width: 33.33333333%; height: auto; text-align: left;">
            <h4 style="text-transform: capitalize; color: #a7a7a7; font-size: 9px;">
                To
            </h4>
            <h3 style="color: #525252; font-size: 10px; margin-top: -8px;">
                @php $to = new \Illuminate\Support\Carbon($booking->package_id->end_date);  @endphp
                {{ $to->format('Y.m.d') }}
            </h3>
        </div>
    </div>
    <br>
    <div style="width: 100%; height: 110px; border-radius: 10px; border: 1px solid #cdcdcd; margin-top: 35px;">
        <figure style="float: left; margin: 0; width: 23%; height: 110px; border-radius: 7px 0 0 7px;">
            @php
                $hotelImage = $booking->package_id->hotel_id->main_image_base64;
            @endphp
            <img style="width: 100%;  height: 100%; margin: 0; float: left; border-radius: 7px 0 0 7px;"
                 src="{{$hotelImage}}">
        </figure>
        <div style="width: 77%;  float: right; height: 110px; padding: 0; ">
            <h2 style="width: 100%; text-align: left; padding-left: 10px;
                color: #525252; font-size: 11px; height: auto;">
                {{$booking->package_id->hotel_id->en_name}}
            </h2>
            <div style="width: 100%; text-align: left; padding-left: 10px; height: 12px; display: block; margin-top: -10px;">
                @php
                    $goldenStarCount = 4;
                    if( $booking->package_id->hotel_id->accomodation == 'two star' ){
                        $goldenStarCount = 2;
                    }elseif( $booking->package_id->hotel_id->accomodation == 'three star' ){
                        $goldenStarCount = 3;
                    }elseif( $booking->package_id->hotel_id->accomodation == 'four star' ){
                        $goldenStarCount = 4;
                    }elseif( $booking->package_id->hotel_id->accomodation == 'five star' ){
                        $goldenStarCount = 5;
                    }
                    $grayStarCount = 5 - $goldenStarCount;
                @endphp
                @for( $i = 0; $i < $goldenStarCount; $i++ )
                    @if( $i === 0 )
                        <span style="width: 10px; height: 10px; margin-top: -8px; background: transparent;">
                        <img style="width: 10px; height: 10px;"
                             src="data:image/jpeg;base64,/9j/2wCEAAEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQECAgICAgICAgICAgMDAwMDAwMDAwMBAQEBAQEBAgEBAgICAQICAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDA//dAAQAC//uAA5BZG9iZQBkwAAAAAH/wAARCABOAFIDABEAAREBAhEB/8QAsAABAAEFAQEAAAAAAAAAAAAAAAkCBgcICgUEAQEAAQUAAwEAAAAAAAAAAAAACAUGBwkKAgMEARAAAgIBAwIDBAcGBwAAAAAAAQIDBAUABhEHEggTIRQiMUEVFhgjMlHRQ1ZXlZbVJUJEUmFigREAAgEDAgQDBQEKDgMBAAAAAQIDBAURAAYHCBIhEzFBCRQiUWFCFRgZIzJSVVZxlBYXM1NiY3KBkZOV0dPUJCUmQ//aAAwDAAABEQIRAD8A7+NNNNNNNNNNNNa3+Kfej7R6UZKrUsPXye67dfbtR4nKTJWn77WVkHBDCNsbVkhJHwadfz1Az2jfFibhjy119vtk7Qbg3JUxWyFkbDrFJ1TVjjBB6TSwyQMw/JaoT5jWZuBG2l3Dv6GeoQPQ0EbVDgjILLhYh8siRlcD1CHWAfBTvSSLJbo2DbsOYLlWPceHhkc+VDZqSJTy0cKk8CS3BYgcgD4V2P56hR7JPizPT3/cXBa5TuaOqplulDGzHoSWFlgrVjBOOuaOWnkIUD4aZ2+est8zO2kkoqHdlOgEschp5mA7lXBeIn6IyyLk+sgGpDtbytQ+0000000000001//Q7+NNNNNNNNNNNNRf+MjeRzXUOhtSvL3UtnYtBPGCO0ZnNrDettyv4u3HLUX19VYOPTk654fap8VG3ZxwouG1FL1Wra1uXxFGMe/V4SomOR54pRRKM90cSDtk5nJy4bcFs2fLf5VxU3Gc9J/qYSUT/GQyn6jp+msA9K94PsPqFtTdIcpXxuVgGR7fUvibgajlkC/BmOOsydvPwcA/LUKuXHijLwZ44bb4jBytDQXKMVWO+aKfNPWLj1JppZSmfJwreYGMtb826u69oV9hwDNPTt4f0lT44j+zxFXP0yPXU3isrqroyujqGVlIZWVhyrKw5BUg+h+euvCN0lQSxENGwBBByCD3BBHYgjuCPPWsJlKkqwIYHBB9NVa89fmmmmmmmmmmv//R7+NNNNNNNNNfFkshUxGOv5W/KIKOMpWshdmPwhqUoJLNiU/8RwxMf/NUm/Xu27ZsVbuO8yCGz2+kmqZ5D5JDBG0srn6KiM392vpoqOouFZFQUi9VVPKsaD5u7BVH95IGoLt1bgt7r3Lnty3vS1nctfykqc8iL2yzJMkCH5RV43CKPkqga46+JG97lxK4gXriBd+1wvNzqKt1zkJ48rOsa/0IlKxoPRVA9NbTLDaILBZKSyUv8hS08cQPz6FALH6sQWP1J1b+rJ1VtTI+HfeH106S7WuyyiW/iKx23k/XlxawgSrA8rH1aWzjPZ52PzMuuqjka4pfxscs23LvUSeJerZTm11ffLCag6YY2c+ry0vu1Qx9TMda4uMO3f4NcQK+mjXppKh/eYvl0zZZgPosniIPous26lxrGOmmmmmmmmmv/9Lrzu+OC2lyylLp3AaaTyJWa5uGWO20KOVjexFFiWjhmdRyyBmCE9vc3HJ0i3f2vlyiulRFaNiwm1pM6xGe5OsxQEhWlRKIqjsBlkVmCE9HW/T1tLql5X6dqdGqrw/vBUFuinBQEjuFJlyQPIEgZ88DOB8v24sr/DvH/wBR2f7PqnfhgNx/qLQ/6pL/ANLXv+9eoP0xN+7r/wA2vP8Atvbq/cfb/wDMMj+mqJ+F44j/AKoWT95qv9tfZ97DYf0pV/5cerQ354s91b42jmtpnbmJwsWcrJTs5ClcvS2o6hniktQRpLxGVuQRtC/PP3cjfPgjGHGb2mHEji/wyu3DQ2G2WmnvFOIJamCeoeVYTIjTRqr4XE8atBJ1Z/FSOAMkEXDtTl/sO19w024BWVFTJSuXWN0QKX6SFYkd/gYh1x9pR6eep+tams+6aaaz10d6+bg6PUs1jcdiaGbo5i1Vu+z357MAp24IpIJZoDXPBNuExq/I/Yrx8+ZncrPOnvblbtN2sNjtlFd7NdKiKo8OpkljEE0aNG7x+H2JmTwlk6h/+MeCO4OKeI3Ca0cRammrKyolpqqmjZOqNVbrRiGAbq/MPUVx+e30xmX7b26v3H2//MMj+mpWfheOI/6oWT95qv8AbWN/vYbD+lKv/Lj19sXjhzQRRN09xckgHvvFn7cSMefTtjbGTMo4/wCx/Sq0/tft1rCq1Wx7e9R6slymRT+xTSSEdvm7fP6D5n5XraWJju84T0BgQn/ESLn/AAGq/txZX+HeP/qOz/Z9e78MBuP9RaH/AFSX/pa8PvXqD9MTfu6/82r8reNHbUlavJY2NuWOw8ETzx156liukzRqZUgsMldp4VckK5RCy8HtHPAzRb/aw8P56CCeu2fuCOueFGkWJ4ZY1cqC6xylYzIgbIRzHGXUBiik9ItSflqvSTOkN0ojCGIUsrKxAPYsoLBSR5gMcHtk+Z//0+0brX4X8Fvz2zcezhV27u9++exB2+Thc9MeWc24o1PsGQmb/URr2u3rIpLFxrT5tfZ4bO4z+9b74WinsfFF+qSSPHRQXF+5YzIin3epkPc1MalZGyZ42ZjMuf8Ahnxyuu1PDs24/ErNujCq3nNAPIdBJ/GRj+bY5UfkMAAhjP3FtvO7Ty1rBbjxlrEZWm3bPUtx9j8EkJLE45jsV5QOUkjZo3HqpI1z9b62FvHhpuap2dvu31Ns3JStiSGZek4yel0bukkT4zHLGzRyL8SMw76mzZ71a9wW+O6WaeOooJB8Locj6gjzVh9pWAZT2IB14erQ1U9NNNNNNNNNNNNNNNVxxyTSRxRRvLLK6xxRRqzySSOwVI40UFnd2IAAHJOvdBBPVTpTUyPJUyOFRFBZmZjhVVRkszEgAAEknA14u6RoZJCFjUEkk4AA7kknsAB5nW9XRTwmT3xT3P1ShmqUyUsUdnq7Q27S/iR8/KnElOBhwfZo2WY/tGTgo243lK9mhV3kUvELmMilprWemWnsgYpNMPNWuTrhoIz5+6xss58pnhw0LxY4m8wEVJ4lj2IyyVPdXq8ZRT6iAHs7f1jAoPsBshhv9XwuHqV4KlXFY6tVqwxV61eClWihgghRY4YYYkiVI4oo1CqoAAA4Gt19DtXbFsoobbbrdQwW+niSKKKOCJI4441CpGiKoVURQFVVACqAAABqJU1yuNRM1RPPM87sWZmdizMxySSTkkk5JPcnX//U7+NNNY36ldKtodU8QcZuWiPaoEf6LzVQJFlsTK/xerYKsHgcgd8EgeGTgEr3BWXAvH3lv4Ycxm1zt/f9H/7CFW90r4cJWUbt9qGXB6oyceJBKHhkwCydao6Xpsrfu4diXH32yy/iGI8WF8mKUD0ZfRh9l1w6+hwSDFd1Z6Ibv6S3/wDE4fpPbtmUx43c1GGT2CwTyUrXUJdsZkCg58qRir8N5byBWI5xeZjlD4ocs95/+gi+6OxZ5emlu1Oje7yE5KxVC/EaSp6RnwZGKvhjBLMqOyzw4f8AE/bvECl/8FvAvCLmSmcjxF+bIewljz9tRkZHWqEgHDWop6yPpppppppppq6tnbK3Nv3NQYDauKnymQm4Z/LHZWpwdwD279p+IKdSPn1dyOTwq8sQDkjhZwk4g8aN2Q7L4c26a4XqXBbpHTFBHnDTVMxxHBCue7uwycIgaRlRqDuPc1k2nbHu1+nSCkXyz3Z29EjUfE7n0Cg9u5woJEonRjw5bZ6XxQZjKCvuPepQM+WliJo4h2Hvw4GtMoMRX8JtSKJ3HPb5Ssya6JeVDkQ4fcu9PDujcQgvvFooC1a6Zp6JiPijt0TjKEfkmrkUVEgz0injd4TBjiVxlve+Xe3UHXR7Zz2iB+OUehnYefz8JT4a+vWQH1shqeesM6aaa//V7+NNNNNNa4+KndEW3Oj2bqnyzb3RaobdppIquB583t16XyyCT2Y6hKFb07JGQ888AwQ9o9xEpticrd3tx6Dc9w1FPbIAwDfyj+8VD9J/NpaeYK/bw5XibPV0g5l4DWOS88RaWfuKehjeocjt+SOhBn6ySISPtKGHlkiI/XMdrYNpppppppppre/wSbpjhye8tmTeUr36lPcVFuESV2x8n0fkIS596bmO7A6J/kCOfmSNyvsjOIsFLuDdXCmq8NZaymgudOcAOxpm92qU6vN8rPTuifYCTMBhmIirzOWJ5KG27kj6isUj07juQBIPEjOPId0cE+uUHoMyH63l6h9ppppppr//1u/jTTTTTUa3jT3b9Ibv25s6CXug27ipMndRT6DJZt18uKVfm8GOpxOv5CwfzOtBPtZuJv3b4n2HhZRSZo7FbWq6gA9veq9h0o49WjpYInQ+i1LY8zqanLRt/wB029WbjlXEtZOI0J/m4AckfRpHYH6xj5a0q1qV1JfX10aN3J3K2PxtSzfv3JUgqUqUEtq1ZnkPCQ168KvLNI5+CqCTqpWezXfcN0gslhpaitvNVKscMEEbyzSyMcKkccYZ3dj5KoJPy16KqqpqGnerrZEipI1LO7sFVVHmWYkAAfMnGvc3XszdOx8kMRuzCXcJfaFLEcNtUKTwSDlZq1mF5atqMH3WMbsFcFTwwIF4cSeFHEXhBfhtniVaKu0XpollRJgpWSNh2eKWNnilUH4WMTuEcNG/S6sopdg3JYt0UX3QsFVFVUgYqShOVYejKQGU+oDKMggjIIJtjWPdVvWU+iu7fqT1R2dnnkEVNMtFj8kzN2xrjMwr4u9LLyVVlrQWzMOTwHjB+WpG8pXE3+KPmJ2rvOaQR2tbklNVEnCikrQ1JUO/cAiGOYzgHsHiU+gIsXiZt/8AhPsW5WlR1VBpzJHgd/FhIlQD+0ydBx6MR66mr11p61naaaaaaa//1+/jTTVLukaNJIyoiKzu7sFREUcszMeAqqBySfQDXhLLHDG00zKkKKWZmIAUAZJJPYADuSewGv1VZ2CICWJwAO5JPoNQd9St1vvffu7N1FmaLMZq3NS7+e9MXC/suJhbng90GMrwofQfh+A+A5AeP3EiTi9xo3LxHZmamul2meDqzlaRD4NGhz6x0kcKHsO6+QHYbQ9lWFdsbTt9hAAkpqZFfHkZSOqUj+1Kzn+/zPmfV6adJd49VMoaO26PbSruq5PO3fMhxGLVh3AT2FR2lsuv4IIg8rfHgKCwuTl/5ZeKnMfuE2jYNH02iBwKu4T9SUVICM4klCsXlYd0p4leZs9XSsYeRfg3txA25sOh96vUuapwfCgTBml/srkYUHzdiFHlkthTKT0n6GbN6T00kx0Ayu5JYfLv7nvxL7bL3j7yChDy8eLok8jy4yXcceY8hAI6LeWnk94VctVrWaxQ/dLfskXTU3aoQe8PkfFHTpllpKc9x4cRLuuBPLMVUiCm/wDiluTf9QUrH93sqtmOmjJ6BjyaQ9jK/wDSbsD+Qq5IN9b02LtbqBhpcFuvFV8nScM0DuOy5QsMvatvHW04np2U/wByHhh7rBlJU5j4r8HuHfG3asuzuJFthuFpcExsw6Z6eQjAmpph+MglX85CAw+CRXjZka1dtbpvu0bkt0sNQ8FSMdQHdJF/MkQ/C6n5EdvNSGAIjE6y+GzdPTM2c1h/P3NstGeQ5KGIHI4eH1KrnKkKgLEi+htRDyCRy4iLKh56uavkF4i8vzVG7dreNuDhOrFvekTNVQp3IFwhQYCKOxrIl93JGZVpmdIzOHhvxpsW9glsuPRRblIA8Mn8XMfnA59T/NMesfZMgBYa1agDrNWpseje7frv0y2fuJ5BJbsYiGnkm7u5jlMWz4zIO4JZkM9qo0gDHnscH1BBPW9yrcTP43uX3a2+ZpBJc57YkNUc5PvlITSVLN3JUyTQvKoYk9DqckEE6y+I+3/4Mb3uNnUdNOlQXj7dvClxJGB6HpVwpI7ZU+WMDJupBasjTTTX/9Dv4001ZnUarkL2wN60sTZjqZG1tbO16lmVnSOGWXG2UDs8ccskfAJ4ZVZlPqPUaxTx1t18u/BXdlq23OlLfanblwjhlcsqxu9LKoYsquy4BOGVWZThgMgauTZs9HS7ttlTXoZKOOvgZ1GCWAkU4wSAf2EgHyOo3/Dx4fKnVJZtz7jynkbYxt805MTQaRMplLUSJM0M1lohFQoFZF7njMkzjuVfKPDjQxyNckFs5i45eIe+7j4XDygrTA1HTF1q6uZFVykkpUJT0+GXqeJpJ5B1onu56ZhM7jBxeqNiMtjs0HVfJ4usSyYMUSkkAhc5kk7HAYKi9iesZTUnuEwWH21i6uFwONp4nFUk8urRowrBBECe5m7V9XlkclndiXdiWYkkk9DO0dn7W2Ft6m2nsygpbZtykTphp6dBHGgzknA/KdmJZ3Yl5HJd2ZiSYOXS63G9V0lyu00lRXynLO5LMfl+wAdgBgAAAAAAa9bVya+DTTTVLKrqyOqujqVZWAZWVhwysp5BUg+o+evB40lQxSgNGwIIIyCD2IIPYgjsQfPX6rFSGUkMDkEemtH+vXhf29aoZjfOx5Ku27lGrayeYwbo6YO7DXjksWJ8cleKV8VcKqfukU1nPACxHuZtQnOb7PDZFys104v8H3prBdaOnmq623srLb50iVpJJKVYkdqOcgH8UiGlkPSFSnPW7yg4UccrvBV0+190CStppXWKGcEGdCxCqshYgSpn7RPiL3yZOwF0+DOtkYel2Sms2YpaFvdeQlxtdfMM1Xy6WPr3BI7cIEmnhDKighfeYnlyFyN7KWgvlLy6V1XX1EcllqtyVL0sQ6i8PTBTRTh2OFCvInWiICF+JyxaQqlC5kpqOTfUMcKMtXHQRiRu2Gy8jJgDvkKcEnz7DGFBO3OtnGo96aaa/9k=">
                    </span>
                    @else
                        <span style="width: 10px; height: 10px; margin-top: -8px; margin-left: -9px; background: transparent;">
                        <img style="width: 10px; height: 10px;"
                             src="data:image/jpeg;base64,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">
                    </span>
                    @endif
                @endfor
                @for( $i = 0; $i < $grayStarCount; $i++ )
                    <span style="width: 10px; height: 10px; margin-top: -8px; margin-left: -9px; background: transparent;">
                        <img style="width: 10px; height: 10px;"
                             src="data:image/jpeg;base64,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">
                    </span>
                @endfor
            </div>
            <br>
            <div style="width: 100%; padding-left: 10px; text-align: left; height: auto; display: block;">
                <div style="font-size: 10px; width: 20%; color: #a9a9a9; float: left; ">
                    Location
                </div>
                <div style="font-size: 10px; width: 78.5%; display: block; text-align: right;
                     color: #505050; float: left; margin-left: 20%; ">
                    {{$booking->package_id->hotel_id->address}}
                </div>
            </div>
            @if( !empty($booking->package_id->hotel_id->price) )
                <br>
                <div style="width: 100%; padding-left: 10px; text-align: left; height: auto; display: block;">
                    <div style="font-size: 10px; width: 20%; color: #a9a9a9; float: left; ">
                        Price
                    </div>
                    <div style="font-size: 10px; width: 78.5%; display: block; text-align: right;
                     color: #505050; float: left; margin-left: 20%; ">
                        {{'$'.$booking->package_id->hotel_id->price}}
                    </div>
                </div>
            @endif
            @if( !empty($booking->package_id->hotel_id->price_double) )
                <br>
                <div style="width: 100%; padding-left: 10px; text-align: left; height: auto; display: block;">
                    <div style="font-size: 10px; width: 20%; color: #a9a9a9; float: left; ">
                        Price(double)
                    </div>
                    <div style="font-size: 10px; width: 78.5%; display: block; text-align: right;
                     color: #505050; float: left; margin-left: 20%; ">
                        {{'$'.$booking->package_id->hotel_id->price_double}}
                    </div>
                </div>
            @endif
            @if( !empty($booking->package_id->hotel_id->price_triple) )
                <br>
                <div style="width: 100%; padding-left: 10px; text-align: left; height: auto; display: block;">
                    <div style="font-size: 10px; width: 20%; color: #a9a9a9; float: left; ">
                        Price(trible)
                    </div>
                    <div style="font-size: 10px; width: 78.5%; display: block; text-align: right;
                     color: #505050; float: left; margin-left: 20%; ">
                        {{'$'.$booking->package_id->hotel_id->price_triple}}
                    </div>
                </div>
            @endif
            @if( !empty($booking->package_id->hotel_id->price_quad) )
                <br>
                <div style="width: 100%; padding-left: 10px; text-align: left; height: auto; display: block;">
                    <div style="font-size: 10px; width: 20%; color: #a9a9a9; float: left; ">
                        Price(quad)
                    </div>
                    <div style="font-size: 10px; width: 78.5%; display: block; text-align: right;
                     color: #505050; float: left; margin-left: 20%; ">
                        {{'$'.$booking->package_id->hotel_id->price_quad}}
                    </div>
                </div>
            @endif
        </div>
    </div>
    <br>
    @php $flight = $booking->package_id->airline_id; @endphp
    <div style="width: 100%; height: 70px; border-radius: 10px; border: 1px solid #cdcdcd; margin-top: -8px;">
        <div style="width: 49%; float: left; margin: 0; padding: 0; height: auto; display: block;">
            <figure style="margin: 5px 0 0 10px; padding: 0; width: 25px; height: 25px; float: left;">
                <img style="width: 100%; height: 100%;" src="{{$flight->logo_base64}}">
            </figure>
            <div style="width: 30%; height: 25px; display: block; padding: 0;
                        margin: 5px 0 0 40px;">
                <h4 style="color: #222; float: left; text-align: left; margin: 0; padding: 0; font-size: 8px; width: 100%;">
                    {{$flight->name}}
                </h4>
                <br>
                <div style="width: 100%; text-align: left; height: 11px; display: block; float: left; margin-top: -14px;">
                    @php
                        $flightGoldenStarCount = $flight->rate_avg;
                        $flightGrayStarCount = 5 - $goldenStarCount;
                    @endphp
                    @for( $i = 0; $i < $flightGoldenStarCount; $i++ )
                        @if( $i === 0 )
                            <span style="width: 7px; height: 7px; margin-top: -8px; background: transparent;">
                                <img style="width: 7px; height: 7px;"
                                    src="data:image/jpeg;base64,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">
                            </span>
                        @else
                            <span style="width: 7px; height: 7px; margin-top: -8px; margin-left: -9px; background: transparent;">
                                <img style="width: 7px; height: 7px;"
                                    src="data:image/jpeg;base64,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">
                            </span>
                        @endif
                    @endfor
                    @for( $i = 0; $i < $flightGrayStarCount; $i++ )
                            <span style="width: 7px; height: 7px; margin-top: -8px; margin-left: -9px; background: transparent;">
                                <img style="width: 7px; height: 7px;"
                                    src="data:image/jpeg;base64,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">
                            </span>
                    @endfor
                </div>
            </div>
        </div>
        <div style="width: 49%; font-size: 9px; padding: 2.5px 5px 0 0; text-align: right;  margin: 0; display: block; float: right; height: 25px;">
            {{'$'.$flight->price}}
        </div>
        <br>
        <div style="width: 100%; height: 30px; display: block;  margin-top: 15px;">
            <div style=" width: 13%; height: 30px; float: left; ">

            </div>
            <div style=" width: 86%; height: 30px; float: left; ">
                <div style="float: left; width: 15%; margin: 0; padding: 0; height: auto; text-align: left;">
                    @php
                        $takeOffDate = $flight->takeoff_date;
                        $takeOffDay = \Illuminate\Support\Carbon::parse($takeOffDate)->format('l');
                        $takeOffDayDate = \Illuminate\Support\Carbon::parse($takeOffDate)->format('jS F');
                    @endphp
                    <h4 style="text-transform: capitalize; color: #a7a7a7; font-size: 8px;">
                        {{$takeOffDay}}
                    </h4>
                    <h3 style="color: #525252; font-size: 8px; margin-top: -8px;">
                        {{$takeOffDayDate}}
                    </h3>
                </div>
                <div style="float: left; width: 15%; margin: 0; margin-left: 15%; height: auto; text-align: left;">
                    <h4 style="text-transform: capitalize; color: #222; font-size: 8px;">
                        {{$flight->origin_name}}
                    </h4>
                    <h3 style="color: #525252; font-size: 8px; margin-top: -8px;">
                        {{$flight->takeoff_time}}
                    </h3>
                </div>
                <div style="float: left; width: 40%; margin: 0; margin-left: 30%; height: auto; text-align: left;">

                </div>
                <div style="float: left; width: 15%; margin: 0; margin-left: 70%; height: auto; text-align: left;">
                    <h4 style="text-transform: capitalize; color: #222; font-size: 8px;">
                        {{$flight->destination_name}}
                    </h4>
                    <h3 style="color: #525252; font-size: 8px; margin-top: -8px;">
                        {{$flight->return_time}}
                    </h3>
                </div>
                <div style="float: left; width: 15%; margin: 0; margin-left: 85%; height: auto; text-align: left;">
                    @php
                        $returnDate = $flight->return_date;
                        $returnDay = \Illuminate\Support\Carbon::parse($returnDate)->format('l');
                        $returnDayDate = \Illuminate\Support\Carbon::parse($returnDate)->format('jS F');
                    @endphp
                    <h4 style="text-transform: capitalize; color: #a7a7a7; font-size: 8px;">
                        {{$returnDay}}
                    </h4>
                    <h3 style="color: #525252; font-size: 8px; margin-top: -8px;">
                        {{$returnDayDate}}
                    </h3>
                </div>
            </div>
            <div style=" width: 0%; height: 30px; float: left; ">

            </div>
        </div>
    </div>
    <br>
    <div style="width: 100%; height: 60px; display: block; margin-top: -12px;">
        <div style="float: left; width: 33.33333333%; height: auto; text-align: left;">
            <h4 style="text-transform: capitalize; color: #a7a7a7; font-size: 9px;">
                Name
            </h4>
            <h3 style="color: #86c7c0; font-size: 10px; margin-top: -8px;">
                {{$booking->name}}
            </h3>
        </div>
        <div style="float: left; width: 33.33333333%; height: auto; text-align: left;">
            <h4 style="text-transform: capitalize; color: #a7a7a7; font-size: 9px;">
                Email Address
            </h4>
            <h3 style="color: #525252; font-size: 10px; margin-top: -8px;">
                @if( empty($booking->email) )
                    ------
                @else
                    {{$booking->email}}
                @endif
            </h3>
        </div>
        <div style="float: left; width: 33.33333333%; height: auto; text-align: left;">
            <h4 style="text-transform: capitalize; color: #a7a7a7; font-size: 9px;">
                Phone Number
            </h4>
            <h3 style="color: #525252; font-size: 10px; margin-top: -8px;">
                @if( empty($booking->phone) )
                    ------
                @else
                    {{$booking->phone}}
                @endif
            </h3>
        </div>
    </div>
    <br>
    <div style="width: 100%; height: 60px; display: block;  margin-top: -20px;">
        <div style="float: left; width: 33.33333333%; height: auto; text-align: left;">
            <h4 style="text-transform: capitalize; color: #a7a7a7; font-size: 9px;">
                From
            </h4>
            <h3 style="color: #525252; font-size: 10px; margin-top: -8px;">
                @if( empty($booking->country_id) )
                    ------
                @else
                    {{\App\Models\Country::find($booking->country_id)->name}}
                @endif
            </h3>
        </div>
        <div style="float: left; width: 33.33333333%; height: auto; text-align: left;">
            <h4 style="text-transform: capitalize; color: #a7a7a7; font-size: 9px;">
                Number Of Nights
            </h4>
            <h3 style="color: #525252; font-size: 10px; margin-top: -8px;">
                @if( empty($booking->num_nights) )
                    ------
                @else
                    {{$booking->num_nights}}
                @endif
            </h3>
        </div>
        <div style="float: left; width: 32%; height: auto; text-align: left;">
            <h4 style="text-transform: capitalize; color: #a7a7a7; font-size: 9px;">
                Number Of Adults
            </h4>
            <h3 style="color: #525252; font-size: 10px; margin-top: -8px;">
                @if( empty($booking->num_adults) )
                    ------
                @else
                    {{$booking->num_adults}}
                @endif
            </h3>
        </div>
    </div>
    <br>
    <div style="width: 100%; height: 60px; display: block; margin-top: -10px;">
        <div style="float: left; width: 33.33333333%; height: auto; text-align: left;">
            <h4 style="text-transform: capitalize; color: #a7a7a7; font-size: 9px;">
                Number Of Childs
            </h4>
            <h3 style="color: #525252; font-size: 10px; margin-top: -8px;">
                @if( empty($booking->num_childs) )
                    ------
                @else
                    {{$booking->num_childs}}
                @endif
            </h3>
        </div>
        <div style="float: left; width: 33.33333333%; height: auto; text-align: left;">
            <h4 style="text-transform: capitalize; color: #a7a7a7; font-size: 9px;">
                Remarks
            </h4>
            <h3 style="color: #525252; font-size: 10px; margin-top: -8px;">
                @if( empty($booking->remarks) )
                    ------
                @else
                    {{$booking->remarks}}
                @endif
            </h3>
        </div>
        <div style="float: left; width: 33.33333333%; height: auto; text-align: left;">

        </div>
    </div>
    <br>
    @if( !empty($booking->passengers) )
        @php $passengers = json_decode($booking->passengers); $indexAdults = 0; $indexChilds = 0 ; @endphp
        @if(intval($booking->num_adults) > 0)
            @foreach($passengers as $passenger)
                @if( $passenger->passenger_type == 'adult' )
                    <h1 style="width: 100%; color: #525252; font-size: 12px; height: 20px;">
                        {{($indexAdults + 1)}}st person identification
                    </h1>
                    <br>
                    <div style="width: 100%; height: 60px; display: block;">
                        <div style="float: left; width: 33.33333333%; height: auto; text-align: left;">
                            <h4 style="text-transform: capitalize; color: #a7a7a7; font-size: 9px;">
                                Name
                            </h4>
                            <h3 style="color: #86c7c0; font-size: 10px; margin-top: -8px;">
                                {{$passenger->full_name}}
                            </h3>
                        </div>
                        <div style="float: left; width: 33.33333333%; height: auto; text-align: left;">
                            <h4 style="text-transform: capitalize; color: #a7a7a7; font-size: 9px;">
                                Gender
                            </h4>
                            <h3 style="color: #525252; font-size: 10px; margin-top: -8px;">
                                @if( empty($passenger->gender) )
                                    ------
                                @else
                                    {{$passenger->gender}}
                                @endif
                            </h3>
                        </div>
                        <div style="float: left; width: 33.33333333%; height: auto; text-align: left;">
                            <h4 style="text-transform: capitalize; color: #a7a7a7; font-size: 9px;">
                                Birth Of Date
                            </h4>
                            <h3 style="color: #525252; font-size: 10px; margin-top: -8px;">
                                @if( empty($passenger->birth_date) )
                                    ------
                                @else
                                    {{$passenger->birth_date}}
                                @endif
                            </h3>
                        </div>
                    </div>
                    <br>
                    <div style="width: 100%; height: 120px; display: block;">
                        <div style="float: left; width: 33.33333333%; height: auto; text-align: left;">
                            <h4 style="text-transform: capitalize; color: #a7a7a7; font-size: 9px;">
                                passport
                            </h4>
                            <img style="height: 100px;" src="{{$passenger->passport_img_b64}}">
                        </div>
                    </div>
                    <br>
                    @php $indexAdults++; @endphp
                @endif
            @endforeach
        @endif
        @if(intval($booking->num_childs) > 0)
            @foreach($passengers as $passenger)
                @if( $passenger->passenger_type == 'child' )
                    <h1 style="width: 100%; color: #525252; font-size: 12px; height: 20px;">
                        {{($indexChilds + 1)}}st child identification
                    </h1>
                    <br>
                    <div style="width: 100%; height: 60px; display: block;">
                        <div style="float: left; width: 33.33333333%; height: auto; text-align: left;">
                            <h4 style="text-transform: capitalize; color: #a7a7a7; font-size: 9px;">
                                Name
                            </h4>
                            <h3 style="color: #86c7c0; font-size: 10px; margin-top: -8px;">
                                {{$passenger->full_name}}
                            </h3>
                        </div>
                        <div style="float: left; width: 33.33333333%; height: auto; text-align: left;">
                            <h4 style="text-transform: capitalize; color: #a7a7a7; font-size: 9px;">
                                Gender
                            </h4>
                            <h3 style="color: #525252; font-size: 10px; margin-top: -8px;">
                                @if( empty($passenger->gender) )
                                    ------
                                @else
                                    {{$passenger->gender}}
                                @endif
                            </h3>
                        </div>
                        <div style="float: left; width: 33.33333333%; height: auto; text-align: left;">
                            <h4 style="text-transform: capitalize; color: #a7a7a7; font-size: 9px;">
                                Birth Of Date
                            </h4>
                            <h3 style="color: #525252; font-size: 10px; margin-top: -8px;">
                                @if( empty($passenger->birth_date) )
                                    ------
                                @else
                                    {{$passenger->birth_date}}
                                @endif
                            </h3>
                        </div>
                    </div>
                    <br>
                    <div style="width: 100%; height: 120px; display: block;">
                        <div style="float: left; width: 33.33333333%; height: auto; text-align: left;">
                            <h4 style="text-transform: capitalize; color: #a7a7a7; font-size: 9px;">
                                passport
                            </h4>
                            <img style="height: 100px;" src="{{$passenger->passport_img_b64}}">
                        </div>
                    </div>
                    <br>
                    @php $indexChilds++; @endphp
                @endif
            @endforeach
        @endif
    @endif
    <h1 style="width: 100%; text-align: right; color: #525252; font-size: 12px; height: 20px;">
        Total Price:&nbsp;&nbsp;<span style="color: #86c7c0; font-weight: bold; ">${{$booking->total_price}}</span>
    </h1>
</div>
</body>
</html>