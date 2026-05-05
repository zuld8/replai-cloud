<?php

namespace App\Http\Controllers\Install;

use App\Helper\EnvironmentManager;
use App\Helper\FinalInstallManager;
use App\Helper\InstalledFileManager;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Request as FacadesRequest; 

class FinalController extends Controller
{
    /**
     * Update installed file and display finished view.
     * 
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function finish(InstalledFileManager $fileManager, FinalInstallManager $finalInstall, EnvironmentManager $environment)
    {
        $finalMessages = $finalInstall->runFinal();

        $deviceName     = getHostName();
        $domain         = substr(FacadesRequest::root(), 7);

        $finalStatusMessage = $fileManager->update();
        $finalEnvFile       = $environment->getEnvContent();

        return view('installer.finished', compact('finalMessages', 'finalStatusMessage', 'finalEnvFile'));
    }
}
