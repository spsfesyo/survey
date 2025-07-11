@extends('layouts.app')

@section('title', 'Undian Doorprize')

@push('style')
    <style>
        canvas {
            border: 2px solid #ccc;
            border-radius: 50%;
        }
    </style>
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Undian Doorprize</h1>
            </div>

            <div class="section-body">
                <!-- SPINNING WHEEL -->
                <div class="row">
                    <div class="col-12">
                        <div class="card mb-4">
                            <div class="card-header">
                                <h4>Spinning Wheel Undian</h4>
                            </div>
                            <div class="card-body text-center">
                                <canvas id="wheelCanvas" width="400" height="400"></canvas>
                                <br>
                                <button id="spinButton" class="btn btn-primary mt-3">
                                    <i class="fas fa-sync-alt"></i> Putar Undian
                                </button>
                                <h5 class="mt-3" id="winnerName"></h5>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TABLE PEMENANG -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h4 class="mb-0">Pemenang Doorprize</h4>
                            </div>
                            <div class="card-body text-center">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Nama Pemenang</th>
                                            </tr>
                                        </thead>
                                        <tbody id="winnerTableBody">
                                            <!-- Baris pemenang akan ditambahkan secara dinamis -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <script>
        const respondents =
        @json($respondents); // misal dari controller dikirim: $respondents = MasterRespondent::pluck('nama_respondent');
        let startAngle = 0;
        let arc = Math.PI * 2 / respondents.length;
        let spinTimeout = null;
        let spinAngleStart = 10;
        let spinTime = 0;
        let spinTimeTotal = 0;
        let ctx;

        function drawWheel() {
            const canvas = document.getElementById("wheelCanvas");
            if (!canvas.getContext) return;
            ctx = canvas.getContext("2d");
            const outsideRadius = 180;
            const textRadius = 140;
            const insideRadius = 40;

            ctx.clearRect(0, 0, canvas.width, canvas.height);
            ctx.strokeStyle = "black";
            ctx.lineWidth = 2;
            ctx.font = '14px Arial';

            for (let i = 0; i < respondents.length; i++) {
                let angle = startAngle + i * arc;
                ctx.fillStyle = i % 2 === 0 ? "#f8b500" : "#fceabb";

                ctx.beginPath();
                ctx.arc(200, 200, outsideRadius, angle, angle + arc, false);
                ctx.arc(200, 200, insideRadius, angle + arc, angle, true);
                ctx.fill();
                ctx.stroke();

                ctx.save();
                ctx.fillStyle = "black";
                ctx.translate(200 + Math.cos(angle + arc / 2) * textRadius,
                    200 + Math.sin(angle + arc / 2) * textRadius);
                ctx.rotate(angle + arc / 2);
                const name = respondents[i];
                ctx.fillText(name, -ctx.measureText(name).width / 2, 0);
                ctx.restore();
            }
        }

        function rotateWheel() {
            spinTime += 30;
            if (spinTime >= spinTimeTotal) {
                stopRotateWheel();
                return;
            }

            const spinAngle = spinAngleStart - easeOut(spinTime, 0, spinAngleStart, spinTimeTotal);
            startAngle += (spinAngle * Math.PI / 180);
            drawWheel();
            spinTimeout = setTimeout(rotateWheel, 30);
        }

        function stopRotateWheel() {
            clearTimeout(spinTimeout);
            const degrees = startAngle * 180 / Math.PI + 90;
            const arcd = arc * 180 / Math.PI;
            const index = Math.floor((360 - degrees % 360) / arcd);
            const winner = respondents[index % respondents.length];

            document.getElementById("winnerName").textContent = "🎉 Pemenang: " + winner;

            // Tambahkan ke tabel
            const tbody = document.getElementById("winnerTableBody");
            const newRow = document.createElement("tr");
            newRow.innerHTML = `
            <td>${tbody.children.length + 1}</td>
            <td>${winner}</td>
        `;
            tbody.appendChild(newRow);

            // Jika tidak ingin nama sama keluar lagi:
            respondents.splice(index, 1);
            arc = Math.PI * 2 / respondents.length;
            drawWheel();
        }

        function easeOut(t, b, c, d) {
            let ts = (t /= d) * t;
            let tc = ts * t;
            return b + c * (tc + -3 * ts + 3 * t);
        }

        document.getElementById("spinButton").addEventListener("click", function() {
            if (respondents.length === 0) {
                alert("Semua peserta sudah menang.");
                return;
            }
            spinAngleStart = Math.random() * 10 + 10;
            spinTime = 0;
            spinTimeTotal = Math.random() * 3000 + 3000;
            rotateWheel();
        });

        window.onload = drawWheel;
    </script>
@endpush
