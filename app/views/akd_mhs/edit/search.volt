<section class="content-header">
  <h1>
    Edit MHS
    <small>it all starts here</small>
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> MHS</a></li>
    <li class="active">Edit MHS</li>
  </ol>
</section>

<!-- Main content -->
<section class="content">
  {#buat input no mhs & nama mhs#}
  <div class="row">
    <div class="col-lg-12">
      <!-- general form elements -->
      <div class="box box-info">
        <div class="box-header with-border">
          <h3 class="box-title">Edit Mahasiswa</h3>
        </div>
        <!-- /.box-header -->
        <!-- form start -->
        <form role="form" id="search">
          <div class="box-body" style="height:190px;">
            <div class="row" id="form_search">
              <div class="col-lg-4">
              </div>

              <div class="col-lg-4">
                <table border="0"width="100%">
                  <h4 for="nomhs" align="center">Masukkan No MHS / Nama MHS</h4>
                  <tr>
                    <td>
                      <div style="margin: 5px;">
                        <input type="text" class="form-control" name="nomhs" id="nomhs" placeholder="Enter No MHS">
                      </div>
                    </td>
                  </tr>

                  <tr>
                    <td>
                      <div style="margin: 5px;" >
                        <input type="text" class="form-control" name="nama" id="nama" placeholder="Enter Nama MHS">
                      </div>
                    </td>
                  </tr>

                  <tr>
                    <td>
                      <div style="margin: 5px;" >
                        <button onclick="search()" type="button" class="btn btn-info" style="align:center; height:auto; width: 100px;" id="btn_search">Search <i class="fa fa-search"></i></button>
                      </div>
                    </td>
                  </tr>
                </table>

              </div>

              <div class="col-lg-4">
              </div>
            </div>
            <!-- ./row -->
          </div>
          <!-- /.box-body -->
          <div class="box-footer">
            <div id="result_data" style="display:none">
              <div id="pencarian">
              </div>
              <table class="table table-hover table-bordered table-striped" id="table" width="100%">
                <thead>
                  <tr>
                    <th>No</th>
                    <th>Angkatan</th>
                    <th>Id Mhs</th>
                    <th>Nama</th>
                  </tr>
                </thead>
              </table>
            </div>
          </div>
        </form>
      </div>
      <!-- /.box -->
    </div>
    <!-- /. col-lg -->
  </div>
  <!-- /. row -->
</section><!-- /.content -->
<script type="text/javascript">
  /////////////////
  // Search Data //
  /////////////////
  $('#result_data').hide();
  function search() {
    $('#result_data').show();
    nomhs = $('#nomhs').val();
    nama  = $('#nama').val();

    if (nomhs||nama) {

      if (nomhs == false) {
        nomhs = "0";
      } else if (nama == false){
        nama = "0";
      }

      huruf = /[0-9]/;
      if (!nomhs.match(huruf)) {
        new PNotify({
          title: 'Warning Notice',
          text: 'Id Mahasiswa harus angka',
          type:'warning'
        });
        $('#result_data').hide();
      }

      var urel = '{{ url('akdMhs/searchEdit/') }}';
      var datas = $('#search').serialize();
      $.ajax({
        type: "POST",
        data: datas,
        url: urel,
        dataType : "json",
      }).done(function( data ) {
      $('#result_data').hide();
        console.log(data);
        // console.log(data.type());
        jmlData = data.length;
        $('#pencarian').html('Hasil Pencarian ('+jmlData+') ditemukan');

        if (jmlData) {
          buatTr = "";
          i = 1;
          for(a = 0; a < jmlData; a++){
            buatTr +=
            '<tr>'+
              '<td>'+i+++'</td>'+
              '<td>'+data[a]["angkatan"]+'</td>'+
              '<td>'+data[a]['id_mhs']+'</td>'+
              '<td>'
                +'<a href=" javascript:void(0) " class="historyAPI" onclick="edit(&quot;'+data[a]['id_mhs']+'&quot;)">'+data[a]['nama']+'</a>'+
              '</td>'+
            '</tr>';
          }

          $('#table').find('tbody').remove().end().append('<tbody>'+buatTr+'</tbody>').DataTable({
            "retrieve": true,
            "lengthChange": false,
            "filter": false,
            "info": false,
            "columnDefs": [
              { "width": "10px", "targets": 0 },
              { "width": "10px", "targets": 1 },
              { "width": "80px", "targets": 2 },
            ]
          });
          $('#result_data').show();

          new PNotify({
            title: 'Regular Notice',
            text: 'Data telah ditemukan',
            type:'success'
          });
        } else {
          $('#table').find('tbody').remove().end();
          new PNotify({
            title: 'Warning Notice',
            text: 'Data tidak ditemukan',
            type:'warning'
          });
          $('#result_data').hide();
        }
      });

    } else {
      $('#result_data').hide();
      new PNotify({
         title: 'Warning Notice',
         text: 'Anda belum memasukkan data',
         type: 'warning'
      });
    }

  }
  // {{ url('akdMhs/edit/') }}'+data[a]['id_mhs']+'
  function edit(id) {
    return load_page('akdMhs/edit/'+id,'page_akdMhs/edit/'+id);
  }
</script>
