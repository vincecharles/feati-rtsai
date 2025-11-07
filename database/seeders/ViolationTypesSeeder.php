<?php

namespace Database\Seeders;

use App\Models\ViolationType;
use Illuminate\Database\Seeder;

class ViolationTypesSeeder extends Seeder
{
    public function run(): void
    {
        $majorOffenses = [
            ['code' => '1', 'name' => 'Assaulting a University authority figure, faculty member, employee, and/or another student', 'class' => '1-21'],
            ['code' => '2', 'name' => 'Instigating, leading, holding or participating in unauthorized rallies, strikes, demonstrations, barricades', 'class' => '1-21'],
            ['code' => '3', 'name' => 'Gross misconduct, disrespect, or dishonesty', 'class' => '1-21'],
            ['code' => '4', 'name' => 'Vandalism, writing on, defacing or destroying University property', 'class' => '1-21'],
            ['code' => '5', 'name' => 'Forging, falsifying or tampering with University records or using tampered credentials', 'class' => '1-21'],
            ['code' => '6', 'name' => 'Carrying, using and/or concealing deadly weapons', 'class' => '1-21'],
            ['code' => '7', 'name' => 'Hazing as prerequisite for membership in fraternities, clubs, associations', 'class' => '1-21'],
            ['code' => '8', 'name' => 'Coercion, intimidation and/or threatening other persons', 'class' => '1-21'],
            ['code' => '9', 'name' => 'Stealing (robbery, theft or qualified theft) the property of others', 'class' => '1-21'],
            ['code' => '10', 'name' => 'Misappropriation of money, funds or property of student councils', 'class' => '1-21'],
            ['code' => '11', 'name' => 'Using fake money or negotiable instruments to pay obligations', 'class' => '1-21'],
            ['code' => '12', 'name' => 'Refusal to submit to investigation or identify oneself', 'class' => '1-21'],
            ['code' => '13', 'name' => 'Selling, using or possessing dangerous, prohibited drugs', 'class' => '1-21'],
            ['code' => '14', 'name' => 'Carrying, possessing obscene/pornographic materials', 'class' => '1-21'],
            ['code' => '15', 'name' => 'Extorting money from others', 'class' => '1-21'],
            ['code' => '16', 'name' => 'Bullying, fighting, causing injury to others', 'class' => '1-21'],
            ['code' => '17', 'name' => 'Immorality/Sexual harassment', 'class' => '1-21'],
            ['code' => '18', 'name' => 'Attempting to bribe school officials', 'class' => '1-21'],
            ['code' => '19', 'name' => 'Plagiarism', 'class' => '1-21'],
            ['code' => '20', 'name' => 'Stealing/Selling examination papers', 'class' => '1-21'],
            ['code' => '21', 'name' => 'Drunkenness or possession of intoxicating liquor', 'class' => '1-21'],
            ['code' => '22', 'name' => 'Tampering with school posters/announcements', 'class' => '22-24'],
            ['code' => '23', 'name' => 'Gambling in any form', 'class' => '22-24'],
            ['code' => '24', 'name' => 'Violation of laws of the land', 'class' => '22-24'],
            ['code' => '25', 'name' => 'Indecency of conduct, language and/or attire', 'class' => '25-26'],
            ['code' => '26', 'name' => 'Cheating / Acts of Academic Dishonesty', 'class' => '25-26'],
            ['code' => '27', 'name' => 'Smoking within school campus', 'class' => '27-29'],
            ['code' => '28', 'name' => 'Spitting, urinating in improper places', 'class' => '27-29'],
            ['code' => '29', 'name' => 'Creating unnecessary noise in academic areas', 'class' => '27-29'],
            ['code' => '30', 'name' => 'Littering in the campus', 'class' => '30-35'],
            ['code' => '31', 'name' => 'Improper use of school facilities', 'class' => '30-35'],
            ['code' => '32', 'name' => 'Improper wearing of uniforms', 'class' => '30-35'],
            ['code' => '33', 'name' => 'Violations of grooming/health protocols', 'class' => '30-35'],
            ['code' => '34', 'name' => 'Improper behavior in class/laboratory/library', 'class' => '30-35'],
            ['code' => '35', 'name' => 'Unauthorized use of school facilities', 'class' => '30-35'],
        ];

        $minorOffenses = [
            ['code' => 'MINOR-1', 'name' => 'Incomplete uniform'],
            ['code' => 'MINOR-2', 'name' => 'Non-wearing of ID'],
            ['code' => 'MINOR-3', 'name' => 'No belt'],
            ['code' => 'MINOR-4', 'name' => 'No nameplate'],
            ['code' => 'MINOR-5', 'name' => 'No scarf'],
            ['code' => 'MINOR-6', 'name' => 'No pin'],
            ['code' => 'MINOR-7', 'name' => 'Unauthorized shoes'],
            ['code' => 'MINOR-8', 'name' => 'Wearing shorts'],
            ['code' => 'MINOR-9', 'name' => 'Wearing distressed jeans'],
            ['code' => 'MINOR-10', 'name' => 'Long hair (for male)'],
            ['code' => 'MINOR-11', 'name' => 'Neon-colored hair'],
            ['code' => 'MINOR-12', 'name' => 'Streaks in hair'],
            ['code' => 'MINOR-13', 'name' => 'Visible piercing devices (for male)'],
        ];

        $penalties = [
            '1-21' => ['1st' => '1 Week Suspension', '2nd' => '2 Weeks Suspension', '3rd' => 'Exclusion/Expulsion'],
            '22-24' => ['1st' => '1 Week Suspension', '2nd' => 'Exclusion', '3rd' => 'Expulsion'],
            '25-26' => ['1st' => '1 Week Suspension', '2nd' => '2 Weeks Suspension', '3rd' => 'Exclusion/Expulsion'],
            '27-29' => ['1st' => 'Citation', '2nd' => '1 Week Suspension', '3rd' => '2 Weeks Suspension', '4th' => 'Exclusion/Expulsion'],
            '30-35' => ['1st' => 'Citation', '2nd' => '3 Days Suspension', '3rd' => '1 Week Suspension', '4th' => '2 Weeks Suspension', '5th' => 'Exclusion/Expulsion'],
        ];

        foreach ($majorOffenses as $offense) {
            ViolationType::create([
                'code' => $offense['code'],
                'name' => $offense['name'],
                'description' => $offense['name'],
                'category' => 'major',
                'offense_class' => $offense['class'],
                'penalties' => json_encode($penalties[$offense['class']] ?? []),
            ]);
        }

        foreach ($minorOffenses as $offense) {
            ViolationType::create([
                'code' => $offense['code'],
                'name' => $offense['name'],
                'description' => $offense['name'],
                'category' => 'minor',
                'offense_class' => 'MINOR',
                'penalties' => json_encode(['note' => 'Based on approved procedures']),
            ]);
        }
    }
}
