<?php

namespace App\Http\Controllers;
ini_set('max_execution_time', 60);

use App\semas\AdminNotify;
use App\semas\BchApi;
use App\semas\GolosApi;
use App\Swi\CurrencyOperations;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use DataTables;
use Jenssegers\Date\Date;
use function Psy\debug;
use ViewComponents\Grids\Component\Column;
use ViewComponents\Grids\Component\ColumnSortingControl;
use ViewComponents\Grids\Component\CsvExport;
use ViewComponents\Grids\Component\PageTotalsRow;
use ViewComponents\Grids\Component\TableCaption;
use ViewComponents\Grids\Grid;
use ViewComponents\ViewComponents\Component\Control\FilterControl;
use ViewComponents\ViewComponents\Component\Control\PageSizeSelectControl;
use ViewComponents\ViewComponents\Component\Control\PaginationControl;
use ViewComponents\ViewComponents\Component\TemplateView;
use ViewComponents\ViewComponents\Customization\CssFrameworks\BootstrapStyling;
use ViewComponents\ViewComponents\Data\ArrayDataProvider;
use ViewComponents\ViewComponents\Data\Operation\FilterOperation;
use ViewComponents\ViewComponents\Input\InputSource;


class TransHistoryController extends Controller
{
    public function index(Request $request)
    {
        try {
            $acc = $request->get('acc', $_acc = 'semasping');
            if (empty($acc)) {
                if (empty($_acc)) {
                    return view(getenv('BCH_API').'.TransHistory.notfound', ['account' => $_acc,
                        'form_action' => 'TransHistoryController@index',]);
                }
                $acc = $_acc;

            }
            $acc = str_replace('@', '', $acc);
            $acc = mb_strtolower($acc);
            $acc = trim($acc);
            $textnotify = 'Старт Запроса на историю #транзакций для аккаунта #' . $acc . ' запрос ' . print_r($request->all(), true) . $request->fullUrl();
            AdminNotify::send($textnotify);
            $data = collect(BchApi::getTransaction($acc, 'transfer'));
            [
                'date',
                'from',
                'to',
                'amount',
                'sum',
                'currency',
                'memo',
                'block',
                'trx_id',
            ];
            $tr = $data->map(function ($item) {
                $ni = $item;
                preg_match('/ (\S*)/', $item['amount'], $cur);
                $ni['date'] = Date::parse($ni['timestamp'])->format('Y.m.d H:i');
                $ni['currency'] = trim($cur[0]);
                $ni['sum'] = str_replace(' GOLOS', '', str_replace(' GBG', '', $item['amount']));
                return $ni;
            });
            $provider = new ArrayDataProvider($tr->sortByDesc('timestamp')->toArray());
            $input = new InputSource($_GET);
            //dump($input);
// create grid
            $grid = new Grid(
                $provider,
                // all components are optional, you can specify only columns
                [
                    new TableCaption('My Grid'),
                    new Column('date'),
                    new Column('from', "От"),
                    new Column('to'),
                    new Column('amount'),
                    new Column('sum'),
                    new Column('currency', 'Валюта'),
                    new Column('memo'),
                    new Column('block'),
                    new Column('trx_id'),

                    new PaginationControl($input->option('page', 1), 25), // 1 - default page, 5 -- page size
                    new PageSizeSelectControl($input->option('page_size', 5), [25, 50, 100, 250, 500, 1000, 5000, 10000]), // allows to select page size
                    new PageTotalsRow([
                        'date' => function () {
                            return 'Page totals';
                        },
                        'sum' => PageTotalsRow::OPERATION_SUM,
                    ]),

                    /*                    (new FilterControl('date', FilterOperation::OPERATOR_EQ, $input->option('d_from'),
                                            new Templateview(getenv('BCH_API').'.input', [
                                                'label' => 'Дата',
                                                'inputType' => 'date',
                                            ]))),*/
                    new FilterControl('sum', FilterOperation::OPERATOR_GTE, $input->option('sum_m'), new TemplateView('input', ['label' => 'Sum >='])),
                    new FilterControl('from', FilterOperation::OPERATOR_NOT_EQ, $input->option('not_from'), new TemplateView('input', ['label' => 'Исключить От'])),
                    new FilterControl('sum', FilterOperation::OPERATOR_LT, $input->option('sum_l'), new TemplateView('input', ['label' => 'Sum <'])),
                    new FilterControl('currency', FilterOperation::OPERATOR_EQ, $input->option('currency'), new TemplateView('select', ['label' => 'Валюта',
                        'options' => [
                            '' => 'Все',
                            'GBG' => 'GBG',
                            'GOLOS' => 'GOLOS',
                        ]
                    ])),
                    //new FilterControl('memo', FilterOperation::OPERATOR_LIKE, $input->option('memo'), new TemplateView('input', ['label' => 'Мемо содержит:'])),
                    //new FilterControl('1', FilterOperation::OPERATOR_NOT_EQ, $input->option('acc'), new TemplateView('input', ['inputType' => 'hidden',])),


                    /*new ColumnSortingControl('date', $input->option('sort')),
                    new ColumnSortingControl('block', $input->option('sort')),*/

                    //new CsvExport($input->option('csv')), // yep, that's so simple, you have CSV export now


                    /* (new Column('age'))
                         ->setValueCalculator(function ($row) {
                             return DateTime
                                 ::createFromFormat('Y-m-d', $row->birthday)
                                 ->diff(new DateTime('now'))
                                 ->y;
                         })
                         ->setValueFormatter(function ($val) {
                             return "$val years";
                         })
                     ,
                     new DetailsRow(new SymfonyVarDump()), // when clicking on data rows, details will be shown
                     new PaginationControl($input->option('page', 1), 5), // 1 - default page, 5 -- page size
                     new PageSizeSelectControl($input->option('page_size', 5), [2, 5, 10]), // allows to select page size
                     new ColumnSortingControl('id', $input->option('sort')),
                     new ColumnSortingControl('birthday', $input->option('sort')),
                     new CsvExport($input->option('csv')), // yep, that's so simple, you have CSV export now
                     new PageTotalsRow([
                         'id' => PageTotalsRow::OPERATION_IGNORE,
                         'age' => PageTotalsRow::OPERATION_AVG
                     ])*/
                ]
            );


//  but also you can add some styling:
            $customization = new BootstrapStyling();
            $customization->apply($grid);
            $grid->getTileRow()->detach()->attachTo($grid->getTableHeading());
            //echo $grid;
            return view(getenv('BCH_API').'.TransHistory.index', ['grid' => $grid, 'account' => $acc, 'form_action' => 'TransHistoryController@index']);
        } catch (Exception $e) {
            echo $e->getMessage();
            $textnotify = 'Ошибка запроса в историю #транзакций. Запрашиваемый аккаунт #' . $acc . ' : ' . $e->getMessage();;
            AdminNotify::send($textnotify);
            GolosApi::disconnect();
            return view(getenv('BCH_API').'.TransHistory.notfound', ['account' => 'vp',
                'form_action' => 'TransHistoryController@index',]);
        }
    }

    public function data(Request $request)
    {
        $accounts = [['account' => $request->get('acc'), 'date' => "false"]];
        $trans = [];
        //dump($request->all(),$accounts);
        foreach ($accounts as $a) {
            try {
                $trans[] = Cache::remember('tr_hi:' . $a['account'], 1, function () use ($a) {
                    $account = $a['account'];
                    $date = $a['date'];
                    $textnotify = 'Старт Запроса на подсчет транзакций для аккаунта ' . $account . ' с датой ' . $date;
                    AdminNotify::send($textnotify);

                    //$max = GolosApi::getHistoryAccountLast($account);

                    $data = collect(BchApi::getTransaction($account, 'transfer'));

                    $tr = $data->map(function ($item) {
                        $ni = $item;
                        preg_match('/ (\S*)/', $item['amount'], $cur);
                        $ni['date'] = Date::parse($ni['timestamp'])->format('Y.m.d H:i');
                        $ni['currency'] = $cur[0];
                        $ni['sum'] = str_replace(' GOLOS', '', str_replace(' GBG', '', $item['amount']));
                        return $ni;
                    });
                    return $tr;
                });
            } catch (Exception $e) {

            }
            //dump($trans);
        }
        //debug($trans);


        $trans = collect(current($trans));
        //$trans = collect([]);
        return DataTables::of($trans)->toJson();

    }

    public function dataByMonth($ym)
    {

    }

    public static function disp(Collection $tr)
    {
        $tr = $tr->map(function ($item) {
            $ni = $item;
            preg_match('/ (\S*)/', $item['amount'], $cur);
            $ni['date'] = Date::parse($ni['timestamp'])->format('Y-m-d H:i');
            $ni['currency'] = $cur[0];
            $ni['sum'] = str_replace(' GOLOS', '', str_replace(' GBG', '', $item['amount']));
            return $ni;
        });
        dump($tr->last());
    }

    public function show(Request $request, $_acc='')
    {
        //dump($request->all());
        $acc = $request->get('account', $request->get('acc', ''));
        $form_action = 'TransHistoryController@show';
        if (empty($acc)) {
            if (empty($_acc)) {
                return view(getenv('BCH_API').'.TransHistory.notfound', ['account' => '',
                    'form_action' => $form_action,]);
            }
            $acc = $_acc;

        }
        $acc = str_replace('@', '', $acc);
        $acc = mb_strtolower($acc);
        $account = trim($acc);

        $textnotify = 'Старт Запроса на историю #транзакций_dt для аккаунта #' . $acc . ' запрос ' . print_r($request->all(), true) . $request->fullUrl();
        AdminNotify::send($textnotify);

        $account_create = (BchApi::getHistoryAccountFirst($acc));
        //AdminNotify::send(print_r(($account_create),true));

        $date_acc_create = Date::parse($account_create['timestamp'])->format('d.m.Y');
        $date = [Date::parse($account_create['timestamp'])->format('d.m.Y'), Date::now()->format('d.m.Y')];

        $date_range = $request->get('d_from', false);
        if ($date_range) {
            $date = explode(' - ', $date_range);
        }

        $tr = ['all' => 'checked', 'tr_in' => '', 'tr_out' => ''];
        //if ($request->get('tr_type')) $tr['all'] = 'checked';
        if ($request->get('tr_type') == 'in') {
            $tr['tr_in'] = 'checked';
            $tr['all'] = '';
        }
        if ($request->get('tr_type') == 'out') {
            $tr['tr_out'] = 'checked';
            $tr['all'] = '';
        }

        return view(getenv('BCH_API').'.TransHistory.show', compact('account', 'acc', 'form_action', 'request', 'date', 'tr', 'date_acc_create'));
    }

    public function dt_show(Request $request, $_acc)
    {

        try {
            $acc = $request->get('account', '');
            if (empty($acc)) {
                if (empty($_acc)) {
                    return view(getenv('BCH_API').'.TransHistory.notfound', ['account' => '',
                        'form_action' => 'TransHistoryController@show',]);
                }
                $acc = $_acc;

            }
            $acc = str_replace('@', '', $acc);
            $acc = mb_strtolower($acc);
            $acc = trim($acc);

            $data = collect(BchApi::getTransaction($acc, 'transfer'));


            $date_range = $request->get('d_from', false);
            if ($date_range) {
                $date = explode(' - ', $date_range);
                //$account_create = GolosApi::getTransaction($acc, 'account_create');
                AdminNotify::send(print_r($date,true));
                $data = $data->filter(function ($item) use ($date) {

                    if (Date::parse($item['timestamp'])->greaterThanOrEqualTo(Date::parse($date[0].'T00:00:00'))
                        &&
                        Date::parse($item['timestamp'])->lessThanOrEqualTo(Date::parse($date[1].'T23:59:59'))
                    ) return true;
                });

            }

            $tr_type = $request->get('tr_type', 'all');
            if ($tr_type=='in') {
                $data = $data->filter(function ($item) use ($acc){
                    if ($item['to']==$acc) return true;
                });
            }
            if ($tr_type=='out') {
                $data = $data->filter(function ($item) use ($acc){
                    if ($item['from']==$acc) return true;
                });
            }

            $tr = $data->map(function ($item) {
                $ni = $item;
                preg_match('/ (\S*)/', $item['amount'], $cur);
                $ni['date'] = Date::parse($ni['timestamp'])->format('Y.m.d H:i');
                $ni['currency'] = trim($cur[0]);
                $ni['sum'] = trim(str_replace(' STEEM', '', str_replace(' SBD', '', $item['amount'])));
                return $ni;
            });

            $curr = $request->get('currency', false);
            if ($curr) {
                $tr = $tr->filter(function ($item) use ($curr) {
                    if (strtolower($item['currency']) == $curr) return true;
                });
            }
            $exclude_sum_less = $request->get('exclude_sum_less', false);
            if ($exclude_sum_less) {
                $tr = $tr->filter(function ($item) use ($exclude_sum_less) {
                    if (($item['sum']) > $exclude_sum_less) return true;
                });
            }
            $exclude_sum_more = $request->get('exclude_sum_more', false);
            if ($exclude_sum_more) {
                $tr = $tr->filter(function ($item) use ($exclude_sum_more) {
                    if (($item['sum']) < $exclude_sum_more) return true;
                });
            }
            $tr = $tr->sortByDesc('timestamp');

            return Datatables::collection($tr)->make(true);

        } catch (Exception $e) {
            echo $e->getMessage();
            $textnotify = 'Ошибка запроса в историю #транзакций_dt. Запрашиваемый аккаунт #' . $acc . ' : ' . $e->getMessage();;
            AdminNotify::send($textnotify);
            //GolosApi::disconnect();
            return view(getenv('BCH_API').'.TransHistory.notfound', ['account' => $acc,
                'form_action' => 'TransHistoryController@dt_show',]);
        }
    }

    public function dt_request(Request $request)
    {

    }

    private function getAccounts()
    {
        if (!file_exists('vox_populi_accounts')) $this->setAccounts();
        $data = file_get_contents('vox_populi_accounts');
        return json_decode($data, true);
    }

    public function addAccount(Request $request)
    {
        $accounts = $this->getAccounts();
        $acc = $request->get('account');
        $acc = str_replace('@', '', $acc);
        $acc = mb_strtolower($acc);
        $acc = trim($acc);
        $date = $request->get('d_from', false);
        $a['account'] = $acc;
        $a['date'] = $date;
        ///////доавить дату для аккаунтов!!!!!!!!!!!!!!!!!!!!!!///////
        $accounts[$acc] = $a;
        $this->setAccounts($accounts);
        return redirect()->action('VoxPopuliController@index')->with('status', 'Аккаунт <b>' . $acc . '</b> добавлен с датой ' . $date);
    }

    private function setAccounts(array $acc = [])
    {
        $data = json_encode($acc);
        return file_put_contents('vox_populi_accounts', $data);
    }

    public function test()
    {
        $accounts = $this->getAccounts();
        $data = [];
        //dump($accounts);
        foreach ($accounts as $a) {
            $account = $a['account'];
            $date = $a['date'];
            $textnotify = 'Старт Запроса на подсчет транзакций для аккаунта ' . $account . ' с датой ' . $date;
            AdminNotify::send($textnotify);

            $max = GolosApi::getHistoryAccountLast($account);

            $data[] = collect($this->getData($account, $max));
        }
        return $data;
    }


    public function grid()
    {
        $data = collect(BchApi::getTransaction('semasping', 'transfer'));

    }

    public function show_withdraw_example(Request $request)
    {
        try {
            $acc = $request->get('account', $_acc = '');
            if (empty($acc)) {
                if (empty($_acc)) {
                    return view(getenv('BCH_API').'.TransHistory.notfound', ['account' => $_acc,
                        'form_action' => 'TransHistoryController@show_withdraw_example',]);
                }
                $acc = $_acc;

            }
            $acc = str_replace('@', '', $acc);
            $acc = mb_strtolower($acc);
            $acc = trim($acc);
            $textnotify = 'Старт Запроса на #withdraw для аккаунта #' . $acc . ' запрос ' . print_r($request->all(), true) . $request->fullUrl();
            $start_pay = 3;
            AdminNotify::send($textnotify);
            $data = $this->tranz_in_in($acc, $start_pay);
            //dump($data);
            $provider = new ArrayDataProvider($data);
            $input = new InputSource($_GET);
// create grid
            $grid = new Grid(
                $provider,
                // all components are optional, you can specify only columns
                [
                    new Column('date'),
                    new Column('balance_begin', 'Остаток от выведенной СГ на начало месяца'),
                    new Column('for_pay', "К выплате на начало месяца"),
                    new Column('balance_after_pay', "Остаток после выплаты на начало месяца"),
                    new Column('sg', "Заработано за указанный месяц"),
                    new Column('widrw', "Понижение на конец месяца"),
                    new Column('full_sg', "Всего СГ осталось на конец месяца"),
                    //new Column('withdrw_begin', "Выведено к началу месяца."),
                    //new Column('summ_widrw', "Всего понижено СГ в сумме за все предыдущие месяцы "),
                ]
            );
            $customization = new BootstrapStyling();
            $customization->apply($grid);
            $grid->getTileRow()->detach()->attachTo($grid->getTableHeading());
            //echo $grid;
            return view(getenv('BCH_API').'.TransHistory.withdraw', ['grid' => $grid, 'account' => 'semasping']);
        } catch (Exception $e) {

        }
    }

    public function tranz_in_in($acc, $start_pay)
    {
        //$acc = 'istfak';
        $type2 = 'author_reward';
        $tr2 = GolosApi::getTransaction($acc, $type2);
        $tr2 = collect($tr2);
        //dump($tr2->first());
        $tranz = $tr2->map(function ($item) {
            $ni = $item;
            $ni['gests'] = (str_replace(' GESTS', '', $item['vesting_payout']));
            return $ni;
        });
        $tr_by_month = $tranz->groupBy(function ($item) {
            return Carbon::parse($item['timestamp'])->format('Y-m');
        });
        $tr_by_month = $tr_by_month->map(function ($item, $key) {
            $ni = $item;
            $ni['key'] = $key;
            $ni['sum'] = $item->sum('gests');
            $ni['sg'] = CurrencyOperations::convertToSg($ni['sum']);
            return $ni;
        });
        $tr_by_week = $tranz->groupBy(function ($item) {
            return Carbon::parse($item['timestamp'])->format('W');
        });
        $tr_by_week = $tr_by_week->map(function ($item) {
            $ni = $item;
            $ni['sum'] = $item->sum('gests');
            $ni['sg'] = CurrencyOperations::convertToSg($ni['sum']);
            return $ni;
        });


        $tranz_fake_month = $this->add_fake_withdraw_for_month($tr_by_month, $start_pay);
        //  dump($tranz_fake_month);
        $tranz_fake_week = $this->add_fake_withdraw_for_week($tr_by_week);

        return $tranz_fake_month;
    }


    private function add_fake_withdraw_for_month($tr, $start_pay)
    {

        $full_gests = 0;
        $full_sg = 0;
        $summ_widrw = 0;
        $start_withdraw = $tr->first()['key'];
        $tr_for_pay = [];
        foreach ($tr as $key => $item) {
            $tr_for_pay[Carbon::parse($key)->addMonths($start_pay)->format('Y-m')] = $item;
        }
        $tr_for_pay = collect($tr_for_pay);
        $months = ['2017-02', '2017-03',
            '2017-04', '2017-05', '2017-06', '2017-07', '2017-08', '2017-09', '2017-10', '2017-11', /*'2017-12',*/];
        foreach ($months as $key) {
            $prev_month = Carbon::parse($key)->subMonth()->format('Y-m');
            $next_month = Carbon::parse($key)->addMonth()->format('Y-m');
            $item = $tr->get($key, collect(['sum' => 0, 'sg' => 0]));

            $for_pay = ($tr_for_pay->get($key, collect(['sg' => 0])))->get('sg');
            //dump($for_pay);
            if (!$for_pay) {
                $for_pay = 0;
            }
            $res[$key]['date'] = $key; // на начало
            $res[$key]['for_pay'] = round($for_pay); // на начало

            $res[$key]['balance_begin'] = $summ_widrw;
            $summ_widrw = $summ_widrw - $for_pay;
            $res[$key]['balance_after_pay'] = $summ_widrw;

            if (Carbon::parse($key)->greaterThan(Carbon::parse($start_withdraw))) {
                $widrw = ($full_sg / 13) * 4;
                $res[$key]['widrw'] = round($widrw); // за текущий месяц
                $summ_widrw = $summ_widrw + $widrw;  // сколько всего за все время
                $res[$key]['summ_widrw'] = $summ_widrw;
                $full_sg = $full_sg - $widrw; // вычитаем из будущего то что снимется в следующем месяце.
            }
            $full_gests = $full_gests + $item->get('sum');
            $res[$key]['sg'] = round($item['sg']);
            $res[$key]['full_gests'] = $full_gests;
            $full_sg = $full_sg + $item->get('sg');

            $res[$key]['full_sg'] = round($full_sg);


        }

        $tr_for_pay = collect($tr_for_pay);
        //dump($tr_for_pay);
        /*foreach ($res as $key => $re) {
            //$summ_widrw = 0;
            $re = collect($re);
            //$for_pay = ($tr_for_pay->get($key,collect(['sg'=>0])))->get('sg',(int)0);
            $for_pay = ($tr_for_pay->get($key, collect(['sg' => 0])))->get('sg');
            //dump($for_pay);
            if (!$for_pay) {
                $for_pay = 0;
            }

            $summ_widrw = $re->get('summ_widrw');
            if (!$summ_widrw) {
                $summ_widrw = 0;
            }
            $balance = $summ_widrw - $for_pay;
            $re['date'] = $key;
            $re['summ_widrw'] = round($summ_widrw);
            $re['for_pay'] = round($for_pay);
            $re['balance'] = round($balance);
            /*            echo '<pre>' . $key . "\t" .
                            $re['sg'] . "\t" .
                            $re['full_sg'] . "\t" .
                            $re->get('widrw') . "\t" .
                            $summ_widrw . "\t" .
                            $for_pay."\t" .
                            $balance."\t" .
                            '</pre>';
            $res[$key] = $re->toArray();
        }*/
        return $res;
    }


    private function add_fake_withdraw_for_week($tr)
    {

    }
}

[
    "vote" => "",
    "transfer" => "",
    "comment" => "",
    "transfer_to_vesting" => "",
    "curation_reward" => "",
    "author_reward" => "",
    "account_update" => "",
    "account_create" => "",
    "interest" => "",
    "custom_json" => "",
    "delete_comment" => "",
    "transfer_to_savings" => "",
    "convert" => "",
    "account_witness_vote" => "",
    "fill_convert_request" => "",
    "comment_options" => "",
    "withdraw_vesting" => "",
    "fill_vesting_withdraw" => ""
];
