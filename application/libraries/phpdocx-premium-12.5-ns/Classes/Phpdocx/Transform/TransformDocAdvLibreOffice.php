<?php

namespace Phpdocx\Transform;

require_once dirname(__FILE__) . "/TransformDocAdv.php";
class TransformDocAdvLibreOffice extends TransformDocAdv
{
    public function getStatistics($source)
    {
        if (!file_exists($source)) {
            \Phpdocx\Logger\PhpdocxLogger::logger("The file not exist", "fatal");
        }
        $phpdocxconfig = \Phpdocx\Utilities\PhpdocxUtilities::parseConfig();
        $libreOfficePath = $phpdocxconfig["transform"]["path"];
        $tempFile = realpath($source) . uniqid("_txt");
        passthru($libreOfficePath . " --invisible \"macro:///Standard.Module1.GetStatistics(" . realpath($source) . "," . $tempFile . ")\" ");
        $statistics = [];
        $statisticsFile = fopen($tempFile, "r");
        if (!$statisticsFile) {
            throw new \Exception("Unable to open the stats file.");
        }
        while (($statistic = fgets($statisticsFile)) !== false) {
            $dataStatistic = explode(": ", $statistic);
            $statistics[$dataStatistic[0]] = $dataStatistic[1];
        }
        fclose($statisticsFile);
        return $statistics;
    }
    public function transformDocument($source, $target, $options = [])
    {
        $allowedExtensionsSource = ["doc", "docx", "html", "odt", "rtf", "txt", "xhtml"];
        $allowedExtensionsTarget = ["doc", "docx", "html", "odt", "pdf", "png", "rtf", "txt", "xhtml"];
        $filesExtensions = $this->checkSupportedExtension($source, $target, $allowedExtensionsSource, $allowedExtensionsTarget);
        if (!isset($options["method"])) {
            $options["method"] = "direct";
        }
        if (!isset($options["debug"])) {
            $options["debug"] = false;
        }
        if (!isset($options["toc"])) {
            $options["toc"] = false;
        }
        $renameFiles = true;
        $sourceFileInfo = pathinfo($source);
        $sourceExtension = $sourceFileInfo["extension"];
        $phpdocxconfig = \Phpdocx\Utilities\PhpdocxUtilities::parseConfig();
        $libreOfficePath = $phpdocxconfig["transform"]["path"];
        $customHomeFolder = false;
        if (isset($options["homeFolder"])) {
            $currentHomeFolder = getenv("HOME");
            putenv("HOME=" . $options["homeFolder"]);
            $customHomeFolder = true;
        } else {
            if (isset($phpdocxconfig["transform"]["home_folder"])) {
                $currentHomeFolder = getenv("HOME");
                putenv("HOME=" . $phpdocxconfig["transform"]["home_folder"]);
                $customHomeFolder = true;
            }
        }
        $extraOptions = "";
        if (isset($options["extraOptions"])) {
            $extraOptions = $options["extraOptions"];
        }
        $outputDebug = "";
        if (PHP_OS == "Linux" || PHP_OS == "Darwin" || PHP_OS == " FreeBSD") {
            if (!$options["debug"]) {
                $outputDebug = " > /dev/null 2>&1";
            }
        } else {
            if ((substr(PHP_OS, 0, 3) == "Win" || substr(PHP_OS, 0, 3) == "WIN") && !$options["debug"]) {
                $outputDebug = " > nul 2>&1";
            }
        }
        if (isset($options["outdir"])) {
            $outdir = $options["outdir"];
        } else {
            $outdir = $sourceFileInfo["dirname"];
        }
        if ($options["method"] == "script") {
            passthru("php " . dirname(__FILE__) . "/../lib/convertSimple.php -s " . $source . " -e " . $filesExtensions["targetExtension"] . " -p " . $libreOfficePath . " -t " . $options["toc"] . " -o " . $outdir . $outputDebug);
        } else {
            if (isset($options["toc"]) && $options["toc"] === true && (!isset($options["pdfa1"]) || isset($options["pdfa1"]) && $options["pdfa1"] === false)) {
                if ($filesExtensions["targetExtension"] == "docx") {
                    passthru($libreOfficePath . " " . $extraOptions . " --invisible \"macro:///Standard.Module1.SaveToDocxToc(" . realpath($source) . "," . $target . ")\" " . $outputDebug);
                    $renameFiles = false;
                } else {
                    passthru($libreOfficePath . " " . $extraOptions . " --invisible \"macro:///Standard.Module1.SaveToPdfToc(" . realpath($source) . ")\" " . $outputDebug);
                }
            } else {
                if (isset($options["toc"]) && $options["toc"] === true && isset($options["pdfa1"]) && $options["pdfa1"] === true) {
                    passthru($libreOfficePath . " " . $extraOptions . " --invisible \"macro:///Standard.Module1.SaveToPdfA1Toc(" . realpath($source) . ")\" " . $outputDebug);
                } else {
                    if (isset($options["pdfa1"]) && $options["pdfa1"] === true && (!isset($options["toc"]) || !isset($options["toc"]) || $options["toc"] === false)) {
                        passthru($libreOfficePath . " " . $extraOptions . " --invisible \"macro:///Standard.Module1.SaveToPdfA1(" . realpath($source) . ")\" " . $outputDebug);
                    } else {
                        if (isset($options["comments"]) && $options["comments"] === true) {
                            passthru($libreOfficePath . " " . $extraOptions . " --invisible \"macro:///Standard.Module1.ExportNotesToPdf(" . realpath($source) . ")\" " . $outputDebug);
                        } else {
                            if (isset($options["lossless"]) && $options["lossless"] === true) {
                                passthru($libreOfficePath . " " . $extraOptions . " --invisible \"macro:///Standard.Module1.LosslessPdf(" . realpath($source) . ")\" " . $outputDebug);
                            } else {
                                if (isset($options["formsfields"]) && $options["formsfields"] === true) {
                                    passthru($libreOfficePath . " " . $extraOptions . " --invisible \"macro:///Standard.Module1.ExportFormFieldsToPdf(" . realpath($source) . ")\" " . $outputDebug);
                                } else {
                                    passthru($libreOfficePath . " " . $extraOptions . " --invisible --convert-to " . $filesExtensions["targetExtension"] . " " . $source . " --outdir " . $outdir . $outputDebug);
                                }
                            }
                        }
                    }
                }
            }
        }
        $newDocumentPath = $outdir . "/" . $sourceFileInfo["filename"] . "." . $filesExtensions["targetExtension"];
        if ($renameFiles) {
            rename($newDocumentPath, $target);
        }
        if ($customHomeFolder) {
            putenv("HOME=" . $currentHomeFolder);
        }
    }
}

?>