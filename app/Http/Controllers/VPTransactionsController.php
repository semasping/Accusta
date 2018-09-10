<?php
/**
 * Created by PhpStorm.
 * User: semasping (semasping@gmail.com)
 * Date: 07.09.2018
 * Time: 19:11
 */

namespace App\Http\Controllers;


class VPTransactionsController extends Controller

{
    public function showGP()
    {
        $arrAccs = [
            '@vox-populi' => ['@vox-populi-gp'],
            '@vp-webdev' => ['@vp-webdev-gp', '',],
            '@vp-golos-tv' => ['@vp-golos-tv-gp', '',],
            '@vpliganovi4kov' => ['@vpliganovi4kovgp', '',],
            '@vp-cryptorostov' => ['@vpcryptorostovgp', '',],
            '@rblogger' => ['@rblogger-gp', '',],
            '@vp-pravogolosa' => ['@vp-pravogolosagp', '',],
            '@vp-papamama' => ['@vp-papamama-gp', '',],
            '@vp-minsk' => ['@vp-minsk-gp', '',],
            '@vox.mens' => ['@vox.mens-gp', '',],
            '@istfak' => ['@istfak-gp', '',],
            '@vp-kulinar-club' => ['@vp-kulinarclubgp', '@vpkulinarclub-gp',],
            '@vp-golos-est' => ['@vp-golos-est-gp', '',],
            '@vp-pedsovet' => ['@vp-pedsovet-gp', '@vp-pedsovetgp',],
            '@vpodessa' => ['@vpodessa-gp', '@vpodessagp',],
            '@just-life' => ['@just-life-gp', '@justlifegp',],
            '@vp-magic-india' => ['@vp-magic-indiagp', '@vp-magik-indiagp',],
            '@vp-handmade' => ['@vp-handmade-gp', '@vp-handmadegp',],
            '@vp-photo.pro' => ['@vp-photo.pro-gp', '@vp-photo.progp',],
            '@vp-zarubezhje' => ['@vp-zarubezhje-gp', '@vp-zarubezhjegp',],
            '@vp-bodyform' => ['@vp-bodyform-gp', '',],
            '@vp-actionlife' => ['@vp-actionlife-gp', '@vp-actionlifegp',],
            '@vp-painting ' => ['@vp-painting-gp', '@vp-paintinggp',],
            '@poesie' => ['@poesie-gp', '@poesiegp',],
            '@bizvoice ' => ['@bizvoice-gp', '@bizvoicegp',],
            '@fractal ' => ['@fractal-gp', '@fractalgp',],
        ];
        $date = false;

        return view( 'golos.VP.transactionsGP',
            [
                'account' => '@semasping',
                'accounts_vp' => $arrAccs,
                'form_action' => 'VPTransactionsController@showGP',
                'date' => $date
            ]);

    }

}