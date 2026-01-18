<?php

class OfficeConverterException extends Exception
{
}
class OfficeConverter
{
    private $file;
    private $bin;
    private $tempPath;
    private $extension;
    private $basename;
    private $prefixExecWithExportHome;
    public function __construct($filename, $tempPath = NULL, $bin = "libreoffice", $prefixExecWithExportHome = true)
    {
        if ($this->open($filename)) {
            $this->setup($tempPath, $bin, $prefixExecWithExportHome);
        }
    }
    public function convertTo($filename)
    {
        $outputExtension = pathinfo($filename, PATHINFO_EXTENSION);
        $supportedExtensions = $this->getAllowedConverter($this->extension);
        if (!in_array($outputExtension, $supportedExtensions)) {
            throw new OfficeConverterException("Output extension(" . $outputExtension . ") not supported for input file(" . $this->basename . ")");
        }
        $outdir = $this->tempPath;
        $shell = shell_exec($this->makeCommand($outdir, $outputExtension));
        if (is_null($shell)) {
            throw new OfficeConverterException("Convertion Failure! Contact Server Admin.");
        }
        return $this->prepOutput($outdir, $filename, $outputExtension);
    }
    protected function open($filename)
    {
        if (!file_exists($filename) || false === realpath($filename)) {
            throw new OfficeConverterException("File does not exist --" . $filename);
        }
        $this->file = realpath($filename);
        return true;
    }
    protected function setup($tempPath, $bin, $prefixExecWithExportHome)
    {
        $this->basename = pathinfo($this->file, PATHINFO_BASENAME);
        $extension = pathinfo($this->file, PATHINFO_EXTENSION);
        if (!array_key_exists($extension, $this->getAllowedConverter())) {
            throw new OfficeConverterException("Input file extension not supported -- " . $extension);
        }
        $this->extension = $extension;
        if (NULL === $tempPath || !is_dir($tempPath)) {
            $tempPath = dirname($this->file);
        }
        if (false === realpath($tempPath)) {
            $this->tempPath = sys_get_temp_dir();
        } else {
            $this->tempPath = realpath($tempPath);
        }
        $this->bin = $bin;
        $this->prefixExecWithExportHome = $prefixExecWithExportHome;
    }
    protected function makeCommand($outputDirectory, $outputExtension)
    {
        $oriFile = escapeshellarg($this->file);
        $outputDirectory = escapeshellarg($outputDirectory);
        return $this->bin . " --headless --convert-to " . $outputExtension . " " . $oriFile . " --outdir " . $outputDirectory;
    }
    protected function prepOutput($outdir, $filename, $outputExtension)
    {
        $DS = DIRECTORY_SEPARATOR;
        $tmpName = ($this->extension ? str_replace($this->extension, "", $this->basename) : $this->basename . ".") . $outputExtension;
        if (rename($outdir . $DS . $tmpName, $outdir . $DS . $filename)) {
            return $outdir . $DS . $filename;
        }
        if (is_file($outdir . $DS . $tmpName)) {
            return $outdir . $DS . $tmpName;
        }
        return NULL;
    }
    private function getAllowedConverter($extension = NULL)
    {
        $allowedConverter = ["" => ["pdf"], "pptx" => ["pdf"], "ppt" => ["pdf"], "pdf" => ["pdf"], "docx" => ["pdf", "odt", "html"], "doc" => ["pdf", "odt", "html"], "wps" => ["pdf", "odt", "html"], "dotx" => ["pdf", "odt", "html"], "docm" => ["pdf", "odt", "html"], "dotm" => ["pdf", "odt", "html"], "dot" => ["pdf", "odt", "html"], "odt" => ["pdf", "html"], "xlsx" => ["pdf"], "xls" => ["pdf"], "png" => ["pdf"], "jpg" => ["pdf"], "jpeg" => ["pdf"], "jfif" => ["pdf"], "PPTX" => ["pdf"], "PPT" => ["pdf"], "PDF" => ["pdf"], "DOCX" => ["pdf", "odt", "html"], "DOC" => ["pdf", "odt", "html"], "WPS" => ["pdf", "odt", "html"], "DOTX" => ["pdf", "odt", "html"], "DOCM" => ["pdf", "odt", "html"], "DOTM" => ["pdf", "odt", "html"], "DOT" => ["pdf", "odt", "html"], "ODT" => ["pdf", "html"], "XLSX" => ["pdf"], "XLS" => ["pdf"], "PNG" => ["pdf"], "JPG" => ["pdf"], "JPEG" => ["pdf"], "JFIF" => ["pdf"], "Pptx" => ["pdf"], "Ppt" => ["pdf"], "Pdf" => ["pdf"], "Docx" => ["pdf", "odt", "html"], "Doc" => ["pdf", "odt", "html"], "Wps" => ["pdf", "odt", "html"], "Dotx" => ["pdf", "odt", "html"], "Docm" => ["pdf", "odt", "html"], "Dotm" => ["pdf", "odt", "html"], "Dot" => ["pdf", "odt", "html"], "Ddt" => ["pdf", "html"], "Xlsx" => ["pdf"], "Xls" => ["pdf"], "Png" => ["pdf"], "Jpg" => ["pdf"], "Jpeg" => ["pdf"], "Jfif" => ["pdf"], "rtf" => ["docx", "txt", "pdf"], "txt" => ["pdf", "odt", "doc", "docx", "html"]];
        if (NULL !== $extension) {
            if (isset($allowedConverter[$extension])) {
                return $allowedConverter[$extension];
            }
            return [];
        }
        return $allowedConverter;
    }
    private function exec($cmd, $input = "")
    {
        if ($this->prefixExecWithExportHome) {
            $home = getenv("HOME");
            if (!is_writable($home)) {
                $cmd = "export HOME=/tmp && " . $cmd;
            }
        }
        $process = proc_open($cmd, [["pipe", "r"], ["pipe", "w"], ["pipe", "w"]], $pipes);
        if (false === $process) {
            throw new OfficeConverterException("Cannot obtain ressource for process to convert file");
        }
        fwrite($pipes[0], $input);
        fclose($pipes[0]);
        $stdout = stream_get_contents($pipes[1]);
        fclose($pipes[1]);
        $stderr = stream_get_contents($pipes[2]);
        fclose($pipes[2]);
        $rtn = proc_close($process);
        return ["stdout" => $stdout, "stderr" => $stderr, "return" => $rtn];
    }
}

?>