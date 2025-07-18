 <?php

defined('BASEPATH') OR exit('No direct script access allowed'); ?>

 <?php include viewPath('includes/headeriframe'); ?>
 <div class="row">
    <div class="col-lg-12">
        <h5 class="card-header mb-2">Jumlah PTK</h5>
        <div class="table-responsive text-nowrap">
            <table class="table table-bordered">
                <thead>
                    <tr class="table-light">
                        <th width="5">No</th>
                        <th>Jenis PTK</th>
                        <th style="text-align: center;">Laki-laki</th>
                        <th style="text-align: center;">Perempuan</th>
                        <th style="text-align: center;">Jumlah</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    <tr>
                        <td>1</td>
                        <td><span class="fw-medium">Guru</span></td>
                        <td style="text-align: center;">7</td>
                        <td style="text-align: center;">9</td>
                        <td style="text-align: center;"><span class="fw-medium">16</span></td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td><span class="fw-medium">Tenaga Administrasi</span></td>
                        <td style="text-align: center;">2</td>
                        <td style="text-align: center;">1</td>
                        <td style="text-align: center;"><span class="fw-medium">3</span></td>
                    </tr>

                </tbody>
                <tfoot class="table-border-bottom-0">
                    <tr class="table-dark">
                        <th colspan="2">Jumlah</th>
                        <th style="text-align: center;">9</th>
                        <th style="text-align: center;">10</th>
                        <th style="text-align: center;">19</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
 </div>
 <?php include viewPath('includes/footeriframe'); ?>