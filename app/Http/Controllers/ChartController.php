<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ChartController extends Controller
{
    //
    public function index()
    {
        return view('components.chart-component');
    }

    public function showChart()
    {
        // Example data (replace with your actual data fetching logic)
        $chartId = uniqid();
        $chartData = [25, 35, 12, 48, 5, 15];
        $labels = ['January', 'February', 'March', 'April', 'May', 'June'];
        $chartTitle = 'Monthly Sales';
        $type = 'pie'; //bar, line, pie, etc.
        
        return view('components.chart-component', compact('chartId', 'chartData', 'labels', 'chartTitle', 'type'));
    }

}