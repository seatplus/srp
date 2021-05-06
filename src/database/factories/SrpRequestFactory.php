<?php

/*
 * MIT License
 *
 * Copyright (c) 2021 seatplus
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 *
 */

namespace Seatplus\Srp\database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Seatplus\Srp\Models\SrpRequest;

class SrpRequestFactory extends Factory
{
    protected $model = SrpRequest::class;

    public function definition()
    {

        //dd(glob('./*'));

        //$file = fopen('src/database/factories/19c919549fb5b4359324fc7938b21f2965f1baf0.json', 'r');

        $killmail = file_get_contents('src/database/factories/19c919549fb5b4359324fc7938b21f2965f1baf0.json');

        return [
            'id'          => md5($killmail),
            'user_id'     => $this->faker->randomNumber(),
            'killmail'    => $killmail,
            'description' => $this->faker->text,
        ];
    }
}
