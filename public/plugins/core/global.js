// $(document).ajaxStart($.blockUI).ajaxStop($.unblockUI);

window.addEventListener('swal:toast', event => {
   Swal.mixin({
      toast: true,
      position: 'top-right',
      iconColor: 'white',
      customClass: {
         popup: 'colored-toast'
      },
      showConfirmButton: false,
      timer: 5000,
      timerProgressBar: true
   }).fire({
      icon: event.detail.type,
      title: event.detail.title,
      text: event.detail.text,
   })
});

window.addEventListener('swal:modal', event => {
   Swal.fire({
      title: event.detail.title,
      text: event.detail.text,
      type: event.detail.type,
   });
});

window.addEventListener('swal:confirm', event => {
   Swal.fire({
      icon: 'warning',
      title: event.detail.title,
      text: event.detail.text,
      showCancelButton: true,
      confirmButtonText: 'Yes',
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
   }).then((result) => {
      if (result.isConfirmed) {
         return true;
      } else {
         return false;
      }
   });
});

window.addEventListener('blockUI', event => {
   $.blockUI({
      message: '<div class="spinner-border text-primary" style="width:50px; height: 50px;"></div><br><span class="text-semibold text-white">' + event.detail.msg + '</span>',
      // timeout: 2000, //unblock after 2 seconds
      overlayCSS: {
         backgroundColor: '#000',
         opacity: 0.5,
         cursor: 'wait'
      },
      css: {
         border: 0,
         padding: 0,
         backgroundColor: 'transparent'
      }
   });
});

window.addEventListener('dataTable', event => {
   alert('ok');
   $(event.detail.element).DataTable();
});

window.addEventListener('unBlockUI', event => {
   $.unblockUI();
});

window.addEventListener('reload', event => {
   location.reload();
});

window.addEventListener('chart:donat', event => {
   console.log('ok');
   document.addEventListener("DOMContentLoaded", () => {
      echarts.init(document.querySelector("#memory_stat")).setOption({
         tooltip: {
            trigger: 'item'
         },
         legend: {
            top: '0%',
            left: 'center'
         },
         series: [{
            name: 'Memory (MiB)',
            type: 'pie',
            radius: ['40%', '80%'],
            avoidLabelOverlap: false,
            label: {
               show: false,
               position: 'center'
            },
            emphasis: {
               label: {
                  show: true,
                  fontSize: '18',
                  fontWeight: 'bold'
               }
            },
            labelLine: {
               show: false
            },
            data: [{
               value: 50,
               name: 'Used'
            },
            {
               value: 100,
               name: 'Free'
            },
            ]
         }]
      });
   });
});

function notif(type, title, text = null) {
   Swal.mixin({
      toast: true,
      position: 'top-right',
      iconColor: 'white',
      customClass: {
         popup: 'colored-toast'
      },
      showConfirmButton: false,
      timer: 5000,
      timerProgressBar: true
   }).fire({
      icon: type,
      title: title,
      text: text,
   })
}

function blokUI(msg){
   $.blockUI({
      message: '<div class="spinner-border text-primary" style="width:50px; height: 50px;"></div><br><span class="text-semibold text-white">' + msg + '</span>',
      // timeout: 2000, //unblock after 2 seconds
      overlayCSS: {
         backgroundColor: '#000',
         opacity: 0.5,
         cursor: 'wait'
      },
      css: {
         border: 0,
         padding: 0,
         backgroundColor: 'transparent'
      }
   });
}

// function ResetModal() {
//    $("h5.modal-title").empty();
//    $("div.modal-body").empty();
//    $("div.modal-footer").empty();
//    $('#modal').modal('hide');
// }

// function AjaxPostData(url) {
//    var response;
//    $.ajax({
//       url: url,
//       async: false,
//       dataType: "json",
//       success: function (data) {
//          response = data;
//       },
//       error: function () {
//          response = "error";
//       },
//    });
//    return response;
// }

// function AjaxGetData(url) {
//    var response;
//    $.ajax({
//       url: url,
//       async: false,
//       type: 'GET',
//       dataType: "json",
//       success: function (data) {
//          response = data;
//       },
//       error: function () {
//          response = "error";
//       },
//    });
//    return response;
// }