<div>
    @push('script')
    <script>
        var ctx = document.getElementById('myChart').getContext('2d');
        var myChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['00:00:00'],
                datasets: [{
                    label: 'Upload (kbps)',
                    data: [0],
                    backgroundColor: [
                        'rgba(255, 99, 132, 1)'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)'
                    ]
                }, {
                    label: 'Download (kbps)',
                    data: [0],
                    backgroundColor: [
                        'rgba(75, 192, 192, 1)'
                    ],
                    borderColor: [
                        'rgba(75, 192, 192, 1)'
                    ]
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                },
                borderWidth: 1,
                showLine: true,
                spanGaps: true,
                animation: false,
            }
        });

        $("select[name=queue_type]").change(function() {
            $("select[name=queue_name").empty();
            resetChart();
            switch ($("select[name=queue_type").val()) {
                case "0": //simple
                    getSimpleSelect();
                    break;
                case "1": //tree
                    getTreeSelect();
                    break;
                default:
                    $("#chart_container").hide();
            }
        });

        function resetChart() {
            myChart.data.labels = ["", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", ""];
            myChart.data.datasets.forEach((dataset) => {
                dataset.data = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
            });
            myChart.update();
        }

        function getSimpleSelect() {
            <?= "let simples = " . json_encode($simples) . ";\n"; ?>

            var html = '<option value="0">Silakan Pilih Queue</option>';
            simples.forEach(val => {
                html += '<option value="' + val.name + '">' + val.name + '</option>';
            });
            $("select[name=queue_name").empty().append(html);

        }

        function getTreeSelect() {
            <?= "let trees = " . json_encode($trees) . ";\n"; ?>

            var html = '<option value="0">Silakan Pilih Queue</option>';
            trees.forEach(val => {
                html += '<option value="' + val.name + '">' + val.name + '</option>';
            });
            $("select[name=queue_name").empty().append(html);
        }

        $("select[name=queue_name]").change(function() {
            if ($("select[name=queue_name]").val() != "0") {
                getChart();
            } else {
                console.log("nop");
            }
            $("#chart_container").show();
        })

        function getChart() {
            setInterval(() => {
                let type = $("select[name=queue_type]").val();
                let name = $("select[name=queue_name]").val();

                $.ajax({
                    url: "{{ route('get-traffic-queue') }}",
                    type: "POST",
                    data: {
                        type: type,
                        name: name,
                        _token: $('#token').val(),
                    },
                    dataType: "json",
                    success: function(data) {
                        UpdateChart(data);
                    },
                    error: function() {},
                });
            }, 1000);
        }

        function UpdateChart(data) {
            myChart.data.labels.push(data.time);
            myChart.data.datasets[0].data.push(data.upload);
            myChart.data.datasets[1].data.push(data.download);

            //remove first
            if (myChart.data.labels.length > 20) {
                myChart.data.labels.shift();
                myChart.data.datasets.forEach((dataset) => {
                    dataset.data.shift();
                });
            }
            myChart.update();
        }
    </script>
    @endpush
</div>