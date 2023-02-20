<?php
/**
 * @OA\Info(
 *   title="OpenAPI Docs for VampyreBytes's Cyberpunk API.",
 *   description="The Cyberpunk API  is unofficial content provided under the Homebrew Content Policy of R. Talsorian Games and is not approved or endorsed by RTG. This content references materials that are the property of R. Talsorian Games and its licensees. This content specifically includes uses content from Cyberpunk Red core rule book, Night City Weather Conditions (Official RTG supplement), and fan supplements from CyberpunkUncensored.com (currently: Weather Conditions by Rob Mulligan and Vehicles of Red by CapriciousNature)",
 *   version="1.2.2",
 *   @OA\Contact(
 *     name="Vampyre Bytes",
 *     email="admin@vampyrebytes.com"
 *   )
 * )
 *
 * @OA\Server(
 *     url="https://cyberpunk.vampyrebytes.com"
 * )
 */

use CyberpunkRedAPI\Choose\CharacterVoice;
use CyberpunkRedAPI\Choose\CompanyName;
use CyberpunkRedAPI\Choose\PersonName;
use CyberpunkRedAPI\Docs\Swagger;
use CyberpunkRedAPI\Generate\CampaignBuilder;
use CyberpunkRedAPI\Generate\CyberpunkUncensoredWeather;
use CyberpunkRedAPI\Generate\NightCityWeather;
use CyberpunkRedAPI\Generate\NightCityWeatherCustom;
use CyberpunkRedAPI\Generate\NightMarket;
use CyberpunkRedAPI\Generate\NightMarketVoR;
use CyberpunkRedAPI\Generate\NomadMarket;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\App;

return function (App $app) {
    $app->get('/', function (
        ServerRequestInterface $request,
        ResponseInterface $response
    ) {
        $response->getBody()->write(json_encode(["Refer to the documentation at /openapi"], JSON_PRETTY_PRINT));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    });

    // OpenAPI docs
    /**
     * @OA\Get(
     *     path="/openapi.json",
     *     summary="Returns the OpenAPI 3.0 documentation in JSON format.",
     *     @OA\Response(
     *         response="200",
     *         description="The OpenAPI 3.0 documentation in JSON format. This documentation provides details on all
     * available API endpoints, including request parameters, response data, and response codes. The file is generated
     * dynamically based on the API documentation provided in the code base.",
     *         @OA\JsonContent(
     *             type="object"
     *         )
     *     )
     * )
     */
    $app->get('/openapi.json', Swagger::class )->setName('openApiDocs');

    /**
     * @OA\Get(
     *     path="/openapi",
     *     summary="Returns the OpenAPI 3.0 documentation in JSON format.",
     *     @OA\Response(
     *         response="200",
     *         description="The OpenAPI 3.0 documentation in JSON format. This documentation provides details on all
     * available API endpoints, including request parameters, response data, and response codes. The file is generated
     * dynamically based on the API documentation provided in the code base.",
     *         @OA\JsonContent(
     *             type="object"
     *         )
     *     )
     * )
     */
    $app->get('/openapi', Swagger::class )->setName('openApiDocsBASE');

    // Name Generator
    /**
     * @OA\Get(
     *     path="/name/{gender}/{firstLastFull}",
     *     summary="Generates a 'real world' name.",
     *     @OA\Parameter(
     *       name="gender",
     *       in="path",
     *       schema={
     *	       "type"="string",
     *         "nullable"=true,
     *         "enum"={"male", "female", "neutral", null}
     *       },
     *       required=true,
     *       allowEmptyValue=false,
     *       description=">
    Gender:
     * `male` - Names typically belonging to males as well as a few neutral that are common among AMAB persons. (This works with 'first', 'full', and null.)
     * `female` - Names typically belonging to females as well as a few neutral that are common among AFAB persons. (This works with 'first', 'full', and null.)
     * `neutral` - Names frequently chosen for being gender-neutral. (Please note that this only works with 'full' or 'first'. Does not work with null.)
     * `null` - Currently, only Male and Female names are available at this time. This does include several names that might be considered gender neutral."
     *     ),
     *     @OA\Parameter(
     *       name="firstLastFull",
     *       in="path",
     *       schema={
     *	       "type"="string",
     *         "nullable"=true,
     *         "enum"={"first", "last", "full", null}
     *       },
     *       required=true,
     *       allowEmptyValue=false,
     *       description=">
    Name Part:
     * `first` - Given Name
     * `last` - Surname only (gender is ignored.)
     * `full` - Full Name (Given name + Surname only)
     * `null` - Full Name, possibly also including Titles and Suffixes"
     *      ),
     *     @OA\Response(
     *         response="200",
     *         description="A randomly generated name",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="name", type="string", example="John Doe")
     *         )
     *     )
     * )
     */
    $app->get('/name[/{gender}[/{firstLastFull}]]', PersonName::class)->setName('generateName');

    // Voice Generator
    /**
     * @OA\Get(
     *     path="/voice/{laban}",
     *     summary="Generates a Vocal pattern, based on, but not limited to, Laban Style for voice acting.",
     *     @OA\Parameter(
     *         name="laban",
     *         in="path",
     *         description="Indicates whether to generate a voice pattern based on Laban (true) or a comprehensive set (false).",
     *         required=true,
     *         @OA\Schema(
     *             type="boolean",
     *             enum={true, false}
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="A randomized vocal pattern, with optional add-ons.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="base_voice",
     *                 type="string",
     *                 description="A string containing the base voice pattern, consisting of 3 factors and/or a Laban style.",
     *                 example="Dabbing - Light, Direct, Sudden"
     *             ),
     *             @OA\Property(
     *                 property="add_ons",
     *                 type="array",
     *                 description="An array of 0 or more properties that add additional details to the voice pattern.",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(
     *                         property="air_source",
     *                         type="string",
     *                         example="Throaty",
     *                         enum={"Throaty", "Nasal"}
     *                     ),
     *                     @OA\Property(
     *                         property="air_variant",
     *                         type="string",
     *                         example="Breathy",
     *                         enum={"Breathy", "Dry"}
     *                     ),
     *                     @OA\Property(
     *                         property="age_variant",
     *                         type="string",
     *                         example="Child",
     *                         enum={"Child", "Old"}
     *                     ),
     *                     @OA\Property(
     *                         property="gender_inclination",
     *                         type="string",
     *                         example="Masc",
     *                         enum={"Masc", "Femme"}
     *                     ),
     *                     @OA\Property(
     *                         property="body_size",
     *                         type="string",
     *                         example="Large",
     *                         enum={"Small", "Large"}
     *                     ),
     *                     @OA\Property(
     *                         property="tempo",
     *                         type="string",
     *                         example="Slow",
     *                         enum={"Slow", "Fast"}
     *                     ),
     *                     @OA\Property(
     *                         property="tone",
     *                         type="string",
     *                         example="Aggressive",
     *                         enum={"Friendly", "Aggressive"}
     *                     ),
     *                     @OA\Property(
     *                         property="impairments",
     *                         type="string",
     *                         example="Strong",
     *                          enum={"Strong", "Mild"}
     *                     )
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    $app->get('/voice[/{laban}]', CharacterVoice::class)->setName('generateVoice');

    /**
     * @OA\Get(
     *     path="/company",
     *     summary="Generates a company name.",
     *     @OA\Response(
     *         response="200",
     *         description="A randomly generated company name. (Should be fictious, but no guarantees. These are generated with census data last names.)",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="companyName",
     *                 type="string",
     *                 description="The generated company name.",
     *             ),
     *         ),
     *     ),
     * )
     */
    $app->get('/company', callable: CompanyName::class)->setName('generateCompany');

    // Quick Campaign Builder (Cybernation Uncensored)
    /**
     * @OA\Get(
     *     path="/generate/campaignhook",
     *     @OA\Response(
     *         response="200",
     *         description="Generates a Campaign scenario from Rob Mulligan's fan supplement 'Cybernation Uncensored Quick Campaign Builder'"
     *     )
     * )
     */
    $app->get('/generate/campaignhook', CampaignBuilder::class)->setName('generateCampaignHook');

    // Night Market Generator
    /**
     * @OA\Get(
     *     path="/generate/nightmarket",
     *     summary="Generates a core RED 'RAW' (Rules As Written) Night Market.",
     *     @OA\Response(
     *         response="200",
     *         description="Generates a Night Market using the rules found in the core Cyberpunk Red rulebook by R. Talsorian Games.",
     *         @OA\JsonContent(
     *             type="object"
     *         )
     *     )
     * )
     */
    $app->get('/generate/nightmarket', NightMarket::class)->setName('generateNightMarket');

    // Vehicles of Red Nomad Market Generator
    /**
     * @OA\Get(
     *     path="/generate/vor/nomadmarket/{specialty}",
     *     summary="Generates a Nomad Market based on the Nomad Family Specialty, if applicable.",
     *     @OA\Parameter(
     *       name="specialty",
     *       in="path",
     *       schema={
     *           "type"="string",
     *           "nullable"=true,
     *           "enum"={null, "Transport", "Medicine", "Weapons", "Upgrades", "Vehicles", "NightMarket"}
     *       },
     *       required=true,
     *       allowEmptyValue=false,
     *       description="Nomad Family Specialty: If the Family has a Specialty, only goods from that specialty will be
    found at the Nomad Market. Without a specialty, a 'Normal' Nomad Market will be generated."
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Generates a Nomad Market using the rules found in the fan supplement Vehicles of Red by Capricious Nature.",
     *         @OA\JsonContent(
     *             type="object"
     *         )
     *     )
     * )
     */
    $app->get('/generate/vor/nomadmarket[/{specialty}]', NomadMarket::class)->setName('generateNomadMarket');

    // Night Market Generator (VoR Add-On)
    /**
     * @OA\Get(
     *     path="/generate/nightmarketvor/{nomad}",
     *     summary=">
    Generates a Night Market using the rules found in the core Cyberpunk Red rulebook by
    R. Talsorian Games, but with Nomad add-ons, per the rules found in the fan supplement
    Vehicles of Red by Capricious Nature.",
     *     @OA\Parameter(
     *       name="nomad",
     *       in="path",
     *       schema={
     *	       "type"="integer",
     *         "nullable"=false,
     *         "enum"={0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10}
     *       },
     *       required=true,
     *       allowEmptyValue=false,
     *       description=">
    Nomad Rank: 0-10. NB: Nomad must be at least Rank 1 before this will alter the table.
    Use 0 or just use the Core Book Night Market API to bypass the Nomad Add-On."
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description=">
    Generates a Night Market using the rules found in the core Cyberpunk Red rulebook by
    R. Talsorian Games, but with Nomad add-ons, per the rules found in the fan supplement
    Vehicles of Red by Capricious Nature.",
     *         @OA\JsonContent(
     *             type="object"
     *         )
     *     )
     * )
     */
    $app->get('/generate/nightmarketvor/{nomad}', NightMarketVoR::class)->setName('generateNightMarketVOR');

    // Night City Weather Generator (R. Talsorian)
    /**
     * @OA\Get(
     *     path="/generate/nc_weather/{month}",
     *     summary="Generates the current temperature and conditions in Night City.",
     *     @OA\Parameter(
     *         name="month",
     *         in="path",
     *         description="The 1-based integer value for the month (1=January, 2=February, etc.)",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             enum={1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12}
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Returns the current temperature and conditions in Night City using the rules found in the official Night City Weather supplement by R. Talsorian Games.",
     *         @OA\JsonContent(
     *             type="object"
     *         )
     *     )
     * )
     */
    $app->get('/generate/nc_weather/{month}', NightCityWeather::class)->setName('generateNCWeather');

    // Night City Weather Generator (Cybernation Uncensored Version)
    /**
     * @OA\Get(
     *     path="/generate/cpu/weather",
     *     @OA\Response(response="200", description="Generates a Weather Condition using the rules found in the fan supplement Weather Conditions supplement by Rob Mulligan of Cybernation Uncensored.")
     * )
     */
    $app->get('/generate/cpu/weather', CyberpunkUncensoredWeather::class)->setName('generateCPUWeather');

    // Night City Weather Generator (R. Talsorian blended with Cybernation Uncensored)
    /**
     * @OA\Get(
     *     path="/generate/custom_weather/{month}",
     *     @OA\Parameter(
     *       name="month",
     *       in="path",
     *       schema={
     *	       "type"="integer",
     *         "nullable"=false,
     *         "enum"={1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12}
     *       },
     *       required=true,
     *       allowEmptyValue=false,
     *       description=" Month: Use the 1-based Integer value for the month, 1=January, 2=February, etc."
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description=">
     Generates the Current Temperature in Night City using the rules found in the official Night City Weather supplement by R. Talsorian Games.
     Also generates Current Conditions and Effects from Rob Mulligan's fan supplement 'Cybernation Uncensored Weather Conditions'"
     *     )
     * )
     */
    $app->get('/generate/custom_weather/{month}', NightCityWeatherCustom::class)->setName('generateCustomWeather');

};
