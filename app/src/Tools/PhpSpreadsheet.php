<?php
/**
 * @author hR.
 */

namespace App\Tools;

use App\Entity\User;
use DateTimeImmutable;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

/** PhpSpreadsheet */
class PhpSpreadsheet
{
    public const PROCEDURAL_MODE = 0;
    public const SPREADSHEET_MODE = 1;

    /**
     * Source : https://stackoverflow.com/questions/5249279/file-get-contents-php-fatal-error-allowed-memory-exhausted/5249971#5249971
     *
     * @param string $file
     * @param mixed  $callback
     *
     * @param string $separator
     * @param string $escape
     *
     * @return bool
     */
    public static function dealWithLargeCsv($file, $callback, $separator = ',', $escape = '\\'): bool
    {
        try {
            $handle = fopen($file, 'r');
            $go = 0;
            while (false !== ($line = fgetcsv($handle, 0, $separator, '"', $escape))) {
                $callback($line, $go);
                unset($line);
                ++$go;
            }

            fclose($handle);
        } catch (Exception $e) {
            trigger_error('dealWithLargeCsv::'.$e->getMessage(), E_USER_NOTICE);

            return false;
        }

        return true;
    }

    /**
     * @param string $path
     * @param int $mode
     * @param bool $avoidEmptyRowsAndCell
     * @param bool $getAllSheet
     * @param bool $slice
     * @param int $sliceStart
     * @param int $sliceStop
     * @param int $sheetIndex
     *
     * @return mixed
     *
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public static function getData(
        string $path,
        $mode = self::SPREADSHEET_MODE,
        bool $avoidEmptyRowsAndCell = false,
        $getAllSheet = false,
        $slice = false,
        $sliceStart = 0,
        $sliceStop = 0,
        $sheetIndex = 0
    ) {
        if (self::PROCEDURAL_MODE === $mode) {
            $schedules = [];
            $file = fopen($path, 'r');
            while (false !== ($line = fgetcsv($file, 100000, ','))) {
                $schedules[] = $line;
                unset($line);
            }
            fclose($file);

            return $schedules;
        }

        /**  Identify the type of $inputFileName  **/
        $inputFileType = IOFactory::identify($path);
        /**  Create a new Reader of the type that has been identified  **/
        $reader = IOFactory::createReader($inputFileType);
        //avoid to read empty rows and cells
        if ($avoidEmptyRowsAndCell) {
            $reader->setReadDataOnly(true);
            $reader->setReadEmptyCells(false);
        }
        $spreadSheet = $reader->load($path); //Load $inputFileName to a Spreadsheet Object
        /**  Convert Spreadsheet Object to an Array for ease of use  **/

        if ($getAllSheet) {
            $schedules = [];
            $allSheet = $spreadSheet->getAllSheets();
            foreach ($allSheet as $sheet) {
                $schedules[$sheet->getTitle()] = $sheet->toArray(null, false);
            }

            return $schedules;
        }

        if ($sheetIndex > 0) {
            $spreadSheet->setActiveSheetIndex($sheetIndex);
        }

        $schedules = $spreadSheet->getActiveSheet()->toArray();

        //if array need slice because of strong empty cell !
        if ($slice) {
            $schedulesSliced = [];
            foreach ($schedules as $schedule) {
                $schedulesSliced[] = array_slice($schedule, $sliceStart, $sliceStop);
            }

            return $schedulesSliced;
        }

        return $schedules;
    }

    /**
     * Check if don't considere cell value.
     *
     * @param string $value
     *
     * @return bool
     */
    public static function cellValueToNotConsider(string $value): bool
    {
        $value = trim($value);

        return
            ('' === $value) ||
            ('-' === $value) ||
            ('N/A' === $value) ||
            ('#ERROR!' === $value)
        ;
    }

    /**
     * Check if cell value is empty, don't consider.
     *
     * @param string $value
     *
     * @return bool
     */
    public static function cellValueIsNotEmpty(string $value): bool
    {
        $value = trim($value);

        return
            (' ' !== $value) &&
            ('' !== $value) &&
            (' - ' !== $value) &&
            ('-' !== $value) &&
            (' -' !== $value) &&
            ('- ' !== $value) &&
            ('"' !== $value) &&
            ('N/A' !== $value) &&
            ('0' !== $value) &&
            ('#ERROR!' !== $value) &&
            (null !== $value)
        ;
    }

    /**
     * @param string $title
     * @param string $fullPathFilename
     * @param array  $headerColumn
     * @param array  $bodyData
     *
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public static function createXlsxFile(string $title, string $fullPathFilename, array $headerColumn, array $bodyData)
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->setActiveSheetIndex(0);
        $sheet = $spreadsheet->getActiveSheet();
        $firstLetterAlphabet = 'A';
        // Excell column
        $i = 1;
        foreach ($headerColumn as $value) {
            $sheet->setCellValue($firstLetterAlphabet++.$i, $value);
        }

        //Body data
        $j = 2;
        foreach ($bodyData as $sheetData) {
            $firstLetterAlphabet = 'A';
            foreach ($sheetData as $sheetDatum) {
                $sheet->setCellValue($firstLetterAlphabet++.$j, $sheetDatum);
            }
            ++$j;
        }

        $sheet->setTitle($title);
        $writer = new Xlsx($spreadsheet);
        $writer->setPreCalculateFormulas(false);
        $writer->setOffice2003Compatibility(true);
        $writer->save($fullPathFilename);
    }
}
