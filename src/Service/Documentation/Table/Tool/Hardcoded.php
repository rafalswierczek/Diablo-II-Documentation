<?php declare(strict_types=1);

namespace App\Service\Documentation\Table\Tool;

use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Service\Utils\ErrorHandler;

/*
    min |  max | +minmax | minmax
	1   |  1:  | +1      |  1
	1   |  2:  | +1-2    |  1-2
	2   |  1:  | +2      |  2
   -1   | -1:  | -1      | -1
   -1   | -2:  | -1-2    | -1-2
   -1   |  1:  | -1-(1)  | -1-(1)
   -2   | -1:  | -2      | -2
    2   | -3   | +2      |  2

descval:
    null: descval not used
    0: [string format] [string format 2]
    1: +[value]% [string format] [string format 2]
    2: [string format] +[value]% [string format 2]

descfunc list: [%s string %d format] | [function? hardcoded_helper()]
    1      +[minmax] [string format]
    2      [minmax]% [string format]
    3      [minmax] [string format]
    4      +[minmax]% [string format]
    5      [minmax*100/128]% [string format]
    6      +[int par] [string format] [string format 2]
    7      [int par]% [string format] [string format 2]
    8      +[int par]% [string format] [string format 2]
    9      [int par] [string format] [string format 2]
    10     [minmax *100/128]% [string format] [string format 2]
    11     printf("%d", 100/$par)
    12     +[minmax] [string format]
    13     +[minmax] [string format]
    14     +[minmax] [string format]
    15     printf("%d %d %s", min, max, skill)
    16     printf("%s %s", minmax, $skill)
    17     +[minmax] [string format] (Increases in Time)
    18     +[minmax]% [string format] (Increases in Time)
    19*     [string format]
    20     [minmax*(-1)]% [string format]
    21     [minmax*(-1)] [string format]
    22     [minmax]% [string format] [monster type]
    23     [minmax]% [string format] [monster name]
    24     printf("%d %s %d %d", $max, $skill, $min, $min)
    25*     [string format]
    26*     [string format]
    27     printf("%s %s %s", $minmax, $skill, $classOnly)
    28     printf("%s %s", $minmax, $skill)
    29*     [string format]
    100    randclassskill
    101    printf("%d %s", $par, $classOnly)
    102    printf("%s", $minmax)
    103    printf("%s", $par|$minmax)
*/

final class Hardcoded extends TableTool
{
    private TranslatorInterface $translator;

    public function __construct(ErrorHandler $errorHandler, TranslatorInterface $translator, SessionInterface $session)
    {
        $this->translator = $translator;
        parent::__construct($errorHandler, $session);
    }

    private function trans(string $code, string $lang)
    {
        return $this->translator->trans($code, [], 'hardcoded', $lang);
    }

    public function getItemTypes(array $tblContent, string $lang): ?array
    {
        $validKeys = $this->checkKeysInTblContent(
            $tblContent,
            $lang,
            'WeaponDescMace',
            'WeaponDescAxe',
            'WeaponDescSword',
            'WeaponDescDagger',
            'WeaponDescJavelin',
            'WeaponDescSpear',
            'WeaponDescBow',
            'WeaponDescStaff',
            'WeaponDescPoleArm',
            'WeaponDescCrossBow',
            'WeaponDescH2H',
            'WeaponDescOrb'
        );

        return $validKeys ?
        [
            // weapons //
            'club' => $tblContent['WeaponDescMace'],
            'hamm' => $tblContent['WeaponDescMace'],
            'mace' => $tblContent['WeaponDescMace'],
            'rod' => $tblContent['WeaponDescMace'],
            'scep' => $tblContent['WeaponDescMace'],
            'axe' => $tblContent['WeaponDescAxe'],
            'taxe' => $tblContent['WeaponDescAxe'],
            'swor' => $tblContent['WeaponDescSword'],
            'knif' => $tblContent['WeaponDescDagger'],
            'tkni' => $tblContent['WeaponDescDagger'],
            'jave' => $tblContent['WeaponDescJavelin'],
            'ajav' => $tblContent['WeaponDescJavelin'],
            'spea' => $tblContent['WeaponDescSpear'],
            'aspe' => $tblContent['WeaponDescSpear'],
            'bow' => $tblContent['WeaponDescBow'],
            'abow' => $tblContent['WeaponDescBow'],
            'xbow' => $tblContent['WeaponDescCrossBow'],
            'staf' => $tblContent['WeaponDescStaff'],
            'wand' => $tblContent['WeaponDescStaff'],
            'pole' => $tblContent['WeaponDescPoleArm'],
            'h2h' => $tblContent['WeaponDescH2H'],
            'h2h2' => $tblContent['WeaponDescH2H'],
            'orb' => $tblContent['WeaponDescOrb'],
            // weapons //
            
            // armor //
            'helm' => $this->trans('class.helm', $lang),
            'tors' => $this->trans('class.armor', $lang),
            'shie' => $this->trans('class.shield', $lang),
            'glov' => $this->trans('class.gloves', $lang),
            'boot' => $this->trans('class.boots', $lang),
            'belt' => $this->trans('class.belt', $lang),
            'phlm' => $this->trans('class.helm.barbarian', $lang),
            'pelt' => $this->trans('class.helm.druid', $lang),
            'ashd' => $this->trans('class.shield.paladin', $lang),
            'head' => $this->trans('class.shield.necromancer', $lang),
            'circ' => $this->trans('class.circlet', $lang),
            // armor //

            // misc //
            'elix' => $this->trans('class.elixir', $lang),
            'hpot' => $this->trans('class.potion.healing', $lang),
            'mpot' => $this->trans('class.potion.mana', $lang),
            'spot' => $this->trans('class.potion.stamina', $lang),
            'apot' => $this->trans('class.potion.antidote', $lang),
            'rpot' => $this->trans('class.potion.rejuvenation', $lang),
            'wpot' => $this->trans('class.potion.thawing', $lang),
            'book' => $this->trans('class.book', $lang),
            'amul' => $this->trans('class.amulet', $lang),
            'ring' => $this->trans('class.ring', $lang),
            'gold' => $this->trans('class.gold', $lang),
            'ques' => $this->trans('class.quest', $lang),
            'bowq' => $this->trans('class.arrow', $lang),
            'torc' => $this->trans('class.torch', $lang),
            'xboq' => $this->trans('class.bolt', $lang),
            'scro' => $this->trans('class.scroll', $lang),
            'body' => $this->trans('class.body', $lang),
            'key' => $this->trans('class.key', $lang),
            'play' => $this->trans('class.ear', $lang),
            'gema' => $this->trans('class.amethyst', $lang),
            'gemt' => $this->trans('class.topaz', $lang),
            'gems' => $this->trans('class.saphire', $lang),
            'geme' => $this->trans('class.emerald', $lang),
            'gemr' => $this->trans('class.ruby', $lang),
            'gemd' => $this->trans('class.diamond', $lang),
            'gemz' => $this->trans('class.skull', $lang),
            'herb' => $this->trans('class.herb', $lang),
            'scha' => $this->trans('class.charm.small', $lang),
            'mcha' => $this->trans('class.charm.medium', $lang),
            'lcha' => $this->trans('class.charm.large', $lang),
            'rune' => $this->trans('class.rune', $lang),
            'jewl' => $this->trans('class.jewel', $lang)
            // misc //
        ] : null;
    }

    public function getCharSkills(array $tblContent): ?array
    {
        $validKeys = $this->checkKeysInTbl(
            $tblContent,
            'ModStr3a',
            'ModStr3d',
            'ModStr3c',
            'ModStr3b',
            'ModStr3e',
            'ModStre8a',
            'ModStre8b'
        );

        return $validKeys ?
        [
            $tblContent['ModStr3a'], // ama
            $tblContent['ModStr3d'], // sor
            $tblContent['ModStr3c'], // nec
            $tblContent['ModStr3b'], // pal
            $tblContent['ModStr3e'], // bar
            $tblContent['ModStre8a'], // dru
            $tblContent['ModStre8b'] // ass
        ] : null;
    }

    public function getStatsHardcoded(array $tblContent): ?array
    {
        $validKeys = $this->checkKeysInTbl(
            $tblContent,
            'StrSkill13',
            'StrSkill14',
            'StrSkill16'
        );

        return $validKeys ?
        [
            [
                'Stat' => 'coldlength',
                'descstrpos' => "{$tblContent['StrSkill13']} %d {$tblContent['StrSkill16']}",
                'descstr2' => null,
                'descfunc' => 25,
                'descval' => null,
                'dgrpstrpos' => null,
                'dgrpstr2' => null,
                'dgrpfunc' => null,
                'dgrpval' => null
            ], [
                'Stat' => 'poisonlength',
                'descstrpos' => "{$tblContent['StrSkill14']} %d {$tblContent['StrSkill16']}",
                'descstr2' => null,
                'descfunc' => 25,
                'descval' => null,
                'dgrpstrpos' => null,
                'dgrpstr2' => null,
                'dgrpfunc' => null,
                'dgrpval' => null
            ]
        ] : null;
    }

    public function getSkilltab(array $tblContent, int $index): string
    {
        $skilltabs = [
            "{$tblContent['ModStre8j']} {$tblContent['strAmazonOnly']}",
            "{$tblContent['ModStre8i']} {$tblContent['strAmazonOnly']}",
            "{$tblContent['ModStre8h']} {$tblContent['strAmazonOnly']}",
            "{$tblContent['ModStre8v']} {$tblContent['strSorceressOnly']}",
            "{$tblContent['ModStre8u']} {$tblContent['strSorceressOnly']}",
            "{$tblContent['ModStre8t']} {$tblContent['strSorceressOnly']}",
            "{$tblContent['ModStre8p']} {$tblContent['strNecromanerOnly']}",
            "{$tblContent['ModStre8o']} {$tblContent['strNecromanerOnly']}",
            "{$tblContent['ModStre8n']} {$tblContent['strNecromanerOnly']}",
            "{$tblContent['ModStre8m']} {$tblContent['strPaladinOnly']}",
            "{$tblContent['ModStre8l']} {$tblContent['strPaladinOnly']}",
            "{$tblContent['ModStre8k']} {$tblContent['strPaladinOnly']}",
            "{$tblContent['ModStre8r']} {$tblContent['strBarbarianOnly']}",
            "{$tblContent['ModStre8s']} {$tblContent['strBarbarianOnly']}",
            "{$tblContent['ModStre8q']} {$tblContent['strBarbarianOnly']}",
            "{$tblContent['ModStre8w']} {$tblContent['strDruidOnly']}",
            "{$tblContent['ModStre8x']} {$tblContent['strDruidOnly']}",
            "{$tblContent['ModStre8y']} {$tblContent['strDruidOnly']}",
            "{$tblContent['ModStre8z']} {$tblContent['strAssassinOnly']}",
            "{$tblContent['ModStre9a']} {$tblContent['strAssassinOnly']}",
            "{$tblContent['ModStre9b']} {$tblContent['strAssassinOnly']}"
        ];

        return $skilltabs[$index];
    }
    
    public function getPropsHardcoded(array $tblContent): ?array
    {
        $validKeys = $this->checkKeysInTbl(
            $tblContent,
            'ModStre8j',
            'ModStre8i',
            'ModStre8h',
            'ModStre8v',
            'ModStre8u',
            'ModStre8t',
            'ModStre8p',
            'ModStre8o',
            'ModStre8n',
            'ModStre8m',
            'ModStre8l',
            'ModStre8k',
            'ModStre8r',
            'ModStre8s',
            'ModStre8q',
            'ModStre8w',
            'ModStre8x',
            'ModStre8y',
            'ModStre8z',
            'ModStre9a',
            'ModStre9b',
            'strAmazonOnly',
            'strSorceressOnly',
            'strNecromanerOnly',
            'strPaladinOnly',
            'strBarbarianOnly',
            'strDruidOnly',
            'strAssassinOnly',
            'Moditemreanimas',
            'strChatLevel',
            'ModStre10d',
            'ModStr1g',
            'ModStr1f',
            'strModEnhancedDamage',
            'Socketable',
            'ModStre9s'
        );

        return $validKeys ? [
            'aura' => [ // changed %d into %s ("min-max")
                'en' => $this->trans('prop.aura', 'en'),
                'pl' => $this->trans('prop.aura', 'pl'),
                'descfunc' => 16,
                'descval' => 0
            ],
            'charged' => [
                'en' => "{$tblContent['strChatLevel']} %s {$tblContent['ModStre10d']}",
                'pl' => "{$tblContent['strChatLevel']} %s {$tblContent['ModStre10d']}",
                'descfunc' => 24,
                'descval' => null
            ],
            'dmg-min' => [
                'en' => $tblContent['ModStr1g'],
                'pl' => $tblContent['ModStr1g'],
                'descfunc' => 1,
                'descval' => 1
            ],
            'dmg-max' => [
                'en' => $tblContent['ModStr1f'],
                'pl' => $tblContent['ModStr1f'],
                'descfunc' => 1,
                'descval' => 1
            ],
            'dmg%' => [
                'en' => $tblContent['strModEnhancedDamage'],
                'pl' => $tblContent['strModEnhancedDamage'],
                'descfunc' => 4,
                'descval' => 1
            ],
            'dur' => [
                'en' => $this->trans('prop.dur', 'en'),
                'pl' => $this->trans('prop.dur', 'pl'),
                'descfunc' => 102,
                'descval' => null
            ],
            'rep-dur' => [
                'en' => $this->trans('prop.repDur', 'en'),
                'pl' => $this->trans('prop.repDur', 'pn'),
                'descfunc' => 11,
                'descval' => null
            ],
            'regen-dur' => [ // Nizari: regen-dur == rep-dur
                'en' => $this->trans('prop.regenDur', 'en'),
                'pl' => $this->trans('prop.regenDur', 'pn'),
                'descfunc' => 11,
                'descval' => null
            ],
            'oskill' => [
                'en' => $this->trans('prop.oskill', 'en'),
                'pl' => $this->trans('prop.oskill', 'pn'),
                'descfunc' => 28,
                'descval' => null
            ],
            'skill' => [
                'en' => $this->trans('prop.skill', 'en'),
                'pl' => $this->trans('prop.skill', 'pn'),
                'descfunc' => 27,
                'descval' => null
            ],
            'skill-rand' => [
                'en' => $this->trans('prop.skillRand', 'en'),
                'pl' => $this->trans('prop.skillRand', 'pn'),
                'descfunc' => 101,
                'descval' => null
            ],
            'skilltab' => [
                'en' => '', // generated later
                'pl' => '', // generated later
                'descfunc' => 14,
                'descval' => null
            ],
            'randclassskill' => [
                'en' => '', // generated later
                'pl' => '', // generated later
                'descfunc' => 100,
                'descval' => null
            ],
            'bloody' => [
                'en' => $this->trans('prop.bloody', 'en'),
                'pl' => $this->trans('prop.bloody', 'pn'),
                'descfunc' => 1,
                'descval' => 0
            ],
            'fade' => [
                'en' => $this->trans('prop.fade', 'en'),
                'pl' => $this->trans('prop.fade', 'pn'),
                'descfunc' => 1,
                'descval' => 0
            ],
            'sock' => [
                'en' => "{$tblContent['Socketable']} (%s)",
                'pl' => "{$tblContent['Socketable']} (%s)",
                'descfunc' => 103,
                'descval' => null
            ],
            'indestruct' => [
                'en' => $tblContent['ModStre9s'],
                'pl' => $tblContent['ModStre9s'],
                'descfunc' => 1,
                'descval' => 0
            ],
            'ethereal' => [
                'en' => $this->trans('prop.ethereal', 'en'),
                'pl' => $this->trans('prop.ethereal', 'pn'),
                'descfunc' => 1,
                'descval' => 0
            ]
        ] : null;
    }
}