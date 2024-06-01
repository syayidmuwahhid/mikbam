<div>
    <div class="row" wire:poll.keep-alive.1000ms="getData">

        <!-- Clock Card -->
        <div class="col-lg-4">
            <div class="card info-card sales-card">

                <!-- <div class="filter">
                    <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                        <li class="dropdown-header text-start">
                            <h6>Setting</h6>
                        </li>

                        <li><a class="dropdown-item" href="#" id="op_clock">Setup Date & Time</a></li>
                    </ul>
                </div> -->

                <div class="card-body">
                    <h5 class="card-title"></span>Date <span>| {{ !empty($clock['hari']) ? $clock['hari'] : '' }}</span></h5>

                    <div class="d-flex align-items-center">
                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                            <i class="bi bi-alarm"></i>
                        </div>
                        <div class="ps-3">
                            <h6>{{ !empty($clock['time']) ? $clock['time'] : '' }}</h6>
                            <span class="text-muted small pt-2 ps-1">{{ !empty($clock['tgl']) ? $clock['tgl']  : ''}}</span>

                        </div>
                    </div>
                    <script type="text/javascript">
                        // $("a#op_clock").click(openClock);
                        // setInterval(()=>{
                        //   $.ajax({
                        //     url: "{{--route('get-clock')--}}",
                        //     type:"POST",
                        //     dataType: "json",
                        //     success: function(data) {
                        //       $("h5#hari").empty();
                        //       $("h6#time").empty();
                        //       $("span#tgl").empty();
                        //       $("h5#hari").append("Date <span>| "+makeDay(new Date(data.date).getDay()));
                        //       $("h6#time").append(data.time);
                        //       $("span#tgl").append(data.date);

                        //     },
                        //     error: function() {
                        //     },
                        //   });
                        // }, 1000);
                    </script>
                </div>

            </div>
        </div><!-- End Clock Card -->

        <!-- Revenue Card -->
        <div class="col-lg-4">
            <div class="card info-card revenue-card">

                <div class="card-body">
                    <h5 class="card-title">{{ !empty($resource['platform']) ? $resource['platform'] : '' }} <span>| {{ !empty($resource['board-name']) ? $resource['board-name'] : '' }}</span></h5>

                    <div class="d-flex align-items-center">
                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                            <i class="bi bi-exclamation-lg"></i>
                        </div>
                        <div class="ps-3">
                            <h6>{{ !empty($resource['model']) ? $resource['model'] : '' }}</h6>
                            <span class='text-muted small pt-2 ps-1'>{{ !empty($resource['version']) ? $resource['version'] : '' }}</span>
                        </div>
                    </div>
                </div>

            </div>
        </div><!-- End Revenue Card -->

        <!-- Revenue Card -->
        <div class="col-lg-4">
            <div class="card info-card revenue-card">

                <div class="card-body">
                    <h5 class="card-title">CPU <span>| {{ !empty($resource['cpu']) ? $resource['cpu'] : '' }}</span></h5>

                    <div class="d-flex align-items-center">
                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                            <i class="bi bi-hdd-network"></i>
                        </div>
                        <div class="ps-3">
                            <h6>Load : {{ !empty($resource['cpu-load']) ? $resource['cpu-load'] : '' }} %</h6>
                            <span class='text-muted small pt-2 ps-1'>{{ !empty($resource['cpu-frequency']) ? $resource['cpu-frequency'] : '' }} MHz</span>
                        </div>
                    </div>
                </div>

            </div><!-- End Revenue Card -->
        </div>
    </div>

    @push('script')
    <script>
        $('#hdd_dashboard_title').html('HDD ({{ !empty($resource["total-hdd-space"]) ? $resource["total-hdd-space"] : "" }} MiB)');
        echarts.init(document.querySelector("#hdd_stat")).setOption({
            tooltip: {
                trigger: 'item'
            },
            legend: {
                top: '0%',
                left: 'center'
            },
            series: [{
                name: 'HDD (MiB)',
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
                        value: '{{ $resource["total-hdd-space"] - $resource["free-hdd-space"] }}',
                        name: 'Used'
                    },
                    {
                        value: '{{ $resource["free-hdd-space"] }}',
                        name: 'Free'
                    },
                ]
            }]
        });

        $('#memory_dashboard_title').html('Memory ({{ !empty($resource["total-memory"]) ? $resource["total-memory"] : "" }} MiB)');
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
                        value: '{{ $resource["total-memory"] - $resource["free-memory"] }}',
                        name: 'Used'
                    },
                    {
                        value: '{{ $resource["free-memory"] }}',
                        name: 'Free'
                    },
                ]
            }]
        });

        var html = '';
        <?= "let interface = " . json_encode($interface) . ";\n"; ?>
        interface.forEach(val => {
            html += '<tr>';
            html += '<td>' + val.name + '</td>'
            html += '<td>' + val.type + '</td>'
            html += '<td>' + (val.running == 'true' ? 'Connected' : '') + '</td>'
            html += '</tr>'
        });
        $('#int_list').append(html);


        <?= "let log = " . json_encode($log) . ";\n"; ?>

        var htmlx = '';
        log.forEach(element => {
            htmlx += '<tr>';
            htmlx += '<td>' + element.waktu + '</td>';
            htmlx += '<td>' + element.deskripsi + '</td>';
            htmlx += '<td>' + element.status + '</td>';
            htmlx += '</tr>';
        });

        $('#log_dashboard').empty().append(htmlx);
    </script>
    @endpush


</div>