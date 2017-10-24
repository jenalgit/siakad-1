<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1' />
  <title>SISKO Al Azhar 29 & 16 BSB City Semarang</title>
  <!-- favicon  -->
  <link rel="icon" href="../img/favicon.png" sizes="32x32" />
  <link rel="icon" href="../img/favicon.png" sizes="192x192" />
  <link rel="apple-touch-icon-precomposed" href="../img/favicon.png" />
  <meta name="msapplication-TileImage" content="../img/favicon.png" /> 

  {{ stylesheet_link("font-awesome/css/font-awesome.min.css") }} 
  {{ stylesheet_link("bootstrap/css/bootstrap.min.css") }}
  {{ stylesheet_link("css/style2.css") }} 

  {{ javascript_include("jquery/dist/jquery.min.js") }} 
  {{ javascript_include("bootstrap/dist/js/bootstrap.min.js") }}
</head>

<body>
  <div class="container">
    <!-- Header  -->
    <div class="row" style="padding: 1em;">
      <div class="col-md-12">
        <div style="margin: 0 auto; ">
          <img src="../img/logo.png" style="height: 6em;float: left; padding: 0 0.5em 0 1em;">
          <img src="../img/logo-himsya.png" style="height: 6em;float: left; padding: 0 0.5em 0 0.5em;">
          <div style="float: left;padding: 1.5em 0 0 1em;">
            <h1 style="text-transform: uppercase;margin: 0;font-size: 2em;color: #0282c6;">
              <b>SISKO Al Azhar 29 & 16 BSB City Semarang</b>
            </h1>
            <p style="color: #333;">Jl. RM. Hadisoebeno Sosro Wardoyo, Mijen, Kedungpane, Jawa Tengah, Kode Pos. 50211, Telp. 08112799510</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Pengumuman  -->
    <div class="col-md-12" style="width: 100%;min-height: 20px;margin-bottom: 1em;">
      <div class="top"></div>
      <div class="pull-left tab-selected">Pengumuman</div>
      <div class="pull-left cells">
        <p style="color: #555;text-align: center;margin-top: 0.7em;">
          {% set teks_berjalan = helper.getBerita('teks_berjalan') %}            
          <marquee>
            {% for v in teks_berjalan %}
              <?php $teks .= strip_tags($v->berita, '<a><strong><em>') . ' | '; ?>
            {% endfor %}
            {{ teks|right_trim('| ') }}
          </marquee>
        </p>
      </div>
    </div>

    <div class="col-md-8">
      <div id="myCarousel" class="carousel slide">
        <ol class="carousel-indicators">
          {% set slider = helper.getSlider() %}            
          {% set no = 1 %}
          {% for v in slider %}
            {% if (no == 1) %}
              <li data-target="#myCarousel" data-slide-to="{{ no }}" class="active"></li>
            {% else %}
              <li data-target="#myCarousel" data-slide-to="{{ no }}"></li>
            {% endif %}
          {% set no += 1 %}
          {% endfor %} 
        </ol>
        <div class="carousel-inner">
          {% set no = 1 %}
          {% for v in slider %}
            {% if (no == 1) %}
              <div class="item active">
            {% else %}
              <div class="item">
            {% endif %}
            <img src="../img/galeri/{{ v.nama }}" class="img-responsive">
            <div class="container">
              <div class="carousel-caption">
                <h2>{{ v.judul }}</h2> 
                <p>{{ v.deskripsi }}</p>
              </div>
            </div>
          </div>
          {% set no += 1 %}
          {% endfor %}         
        </div>
        <!-- Controls -->
        <a class="left carousel-control" href="#myCarousel" data-slide="prev">
          <span class="icon-prev"></span>
        </a>
        <a class="right carousel-control" href="#myCarousel" data-slide="next">
          <span class="icon-next"></span>
        </a>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card card-container" style="margin-top: 0px">
        <label style="width: 100%;text-align: left; color: #0282c6; border-bottom: solid;">
        <h3>AKUN SISKO</h3></label>
        <form class="form-signin" action="{{ url('account/loginProses') }}" method="post">
          <span id="reauth-email" class="reauth-email"></span>
          <label>Login</label>
          <input name="uid" type="text" class="form-control" placeholder="Login">
          <label>Password</label>
          <input name="passwd" type="password" class="form-control" placeholder="Password">
          <p style="color: #999"> Captcha</p>
          <img src="http://kuliahdaring.stiesemarang.ac.id/capcay.php" />
          <input style="    width: 160px;    float: right;" name="ccek" placeholder="Verifikasi" class="form-control" type="text"
          />
          <div id="remember" class="checkbox">
          </div>
          <button class="btn btn-lg btn-primary btn-block btn-signin" type="submit" value="Login">Login</button>
        </form>
        <!-- /form -->
        <a href="#" class="forgot-password" onclick="alert('Silahkan kontak admin untuk mereset password.')">Lupa Password? </a>

      </div>
      <!-- /card-container -->
    </div>
  </div>
  <!-- /container -->
  
  <footer>
    <div class="text-center" style="color: #333;">
      <p>Sistem Informasi Sekolah</p>
      <p style="font-weight:bold;color: #0282c6;"><a href="http://sd-alazhar29.sch.id/" target="_blank">SISKO Al Azhar 29 & 16 BSB City Semarang</a></p>
      <p>Copyright © 2017</p>
    </div>
  </footer>

</body>

</html>

<script type="text/javascript">
  $(document).ready(function () {
    $('#myCarousel').carousel({
      interval: 4000
    });

    var clickEvent = false;
    $('#myCarousel').on('click', '.nav a', function () {
      clickEvent = true;
      $('.nav li').removeClass('active');
      $(this).parent().addClass('active');
    }).on('slid.bs.carousel', function (e) {
      if (!clickEvent) {
        var count = $('.nav').children().length - 1;
        var current = $('.nav li.active');
        current.removeClass('active').next().addClass('active');
        var id = parseInt(current.data('slide-to'));
        if (count == id) {
          $('.nav li').first().addClass('active');
        }
      }
      clickEvent = false;
    });
  });
</script>