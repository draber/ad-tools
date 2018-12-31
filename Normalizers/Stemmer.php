<?php
/**
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to
 * deal in the Software without restriction, including without limitation the
 * rights to use, copy, modify, merge, publish, distribute, sublicense, and/or
 * sell copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace AdTools\Normalizers;

use AdTools\Exceptions\UnsupportedLanguageException;
use Wamania\Snowball\Danish;
use Wamania\Snowball\Dutch;
use Wamania\Snowball\English;
use Wamania\Snowball\French;
use Wamania\Snowball\German;
use Wamania\Snowball\Italian;
use Wamania\Snowball\Norwegian;
use Wamania\Snowball\Portuguese;
use Wamania\Snowball\Romanian;
use Wamania\Snowball\Russian;
use Wamania\Snowball\Spanish;
use Wamania\Snowball\Stem;
use Wamania\Snowball\Swedish;


/**
 * Class Stemmer
 *
 * @package AdTools\Normalizers
 */
class Stemmer
{

    /**
     * Mapping of available stemmers
     *
     * @see vendor/wamania/php-stemmer/src
     * @var array
     */
    private $isoLangMap = [
        'da' => 'Danish',
        'nl' => 'Dutch',
        'en' => 'English',
        'fr' => 'French',
        'de' => 'German',
        'it' => 'Italian',
        'no' => 'Norwegian',
        'pt' => 'Portuguese',
        'ro' => 'Romanian',
        'ru' => 'Russian',
        'es' => 'Spanish',
        'sv' => 'Swedish'
    ];

    private $stemmer;

    /**
     * Stemmer Factory
     *
     * @param $locale
     * @return Stem
     * @throws Exception
     */
    public function __construct($locale)
    {
        $lang = strtolower(substr($locale, 0, 2));
        if (!isset($this->isoLangMap[$lang])) {
            throw new UnsupportedLanguageException(sprintf('No stemmer available for locale "%s"', $locale));
        }
        $stemmerClass = 'Wamania\\Snowball\\' . $this->isoLangMap[$lang];
        $this->stemmer = new $stemmerClass;
    }


    /**
     * Stem a word
     *
     * @param $word
     * @return string
     */
    public function stem($word)
    {
        return $this->stemmer->stem($word);
    }
}
