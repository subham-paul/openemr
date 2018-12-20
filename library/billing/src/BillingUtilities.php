<?php
/*
 * The functions of this class support the billing process like the script billing_process.php.
 *
 * @package OpenEMR
 * @author Rod Roark <rod@sunsetsystems.com>
 * @author Stephen Waite <stephen.waite@cmsvt.com>
 * @copyright Copyright (c) 2011 Rod Roark <rod@sunsetsystems.com>
 * @copyright Copyright (C) 2018 Stephen Waite <stephen.waite@cmsvt.com>
 * @link http://www.open-emr.org
 * @license https://github.com/openemr/openemr/blob/master/LICENSE GNU General Public License 3
 */

namespace OpenEMR\Billing;

class BillingUtilities
{
    const claim_status_codes_CLP02 = array(
        '1'  => 'Processed as Primary',
        '2'  => 'Processed as Secondary',
        '3'  => 'Processed as Tertiary',
        '4'  => 'Denied',
        '5'  => 'Pended',
        '11' => 'Received, but not in process',
        '13' => 'Suspended',
        '15' => 'Suspended - investigation with field',
        '16' => 'Suspended - return with material',
        '17' => 'Suspended - review pending',
        '19' => 'Processed as Primary, Forwarded to Additional Payer(s)',
        '20' => 'Processed as Secondary, Forwarded to Additional Payer(s)',
        '21' => 'Processed as Tertiary, Forwarded to Additional Payer(s)',
        '22' => 'Reversal of Previous Payment',
        '23' => 'Not Our Claim, Forwarded to Additional Payer(s)',
        '25' => 'Predetermination Pricing Only - No Payment',
        '27' => 'Reviewed',
    );

    const claim_adjustment_reason_codes = array(
        '1' => 'Deductible Amount',
        '2' => 'Coinsurance Amount',
        '3' => 'Co-payment Amount',
        '4' => 'The procedure code is inconsistent with the modifier used or a required modifier is missing',
        '5' => 'The procedure code/bill type is inconsistent with the place of service',
        '6' => 'The procedure/revenue code is inconsistent with the patients age',
        '7' => 'The procedure/revenue code is inconsistent with the patients gender',
        '8' => 'The procedure code is inconsistent with the provider type/specialty (taxonomy)',
        '9' => 'The diagnosis is inconsistent with the patients age',
        '10' => 'The diagnosis is inconsistent with the patients gender',
        '11' => 'The diagnosis is inconsistent with the procedure',
        '12' => 'The diagnosis is inconsistent with the provider type',
        '13' => 'The date of death precedes the date of service',
        '14' => 'The date of birth follows the date of service',
        '15' => 'Payment adjusted because the submitted authorization number is missing, invalid, or does not apply to the billed services or provider',
        '16' => 'Claim/service lacks information which is needed for adjudication. Additional information is supplied using remittance advice remarks codes whenever appropriate',
        '17' => 'Payment adjusted because requested information was not provided or was insufficient/incomplete. Additional information is supplied using the remittance advice remarks codes whenever appropriate',
        '18' => 'Duplicate claim/service',
        '19' => 'Claim denied because this is a work-related injury/illness and thus the liability of the Workers Compensation Carrier',
        '20' => 'Claim denied because this injury/illness is covered by the liability carrier',
        '21' => 'Claim denied because this injury/illness is the liability of the no-fault carrier',
        '22' => 'Payment adjusted because this care may be covered by another payer per coordination of benefits',
        '23' => 'Payment adjusted due to the impact of prior payer(s) adjudication including payments and/or adjustments',
        '24' => 'Payment for charges adjusted. Charges are covered under a capitation agreement/managed care plan',
        '25' => 'Payment denied. Your Stop loss deductible has not been met',
        '26' => 'Expenses incurred prior to coverage',
        '27' => 'Expenses incurred after coverage terminated',
        '29' => 'The time limit for filing has expired',
        '31' => 'Claim denied as patient cannot be identified as our insured',
        '32' => 'Our records indicate that this dependent is not an eligible dependent as defined',
        '33' => 'Claim denied. Insured has no dependent coverage',
        '34' => 'Claim denied. Insured has no coverage for newborns',
        '35' => 'Lifetime benefit maximum has been reached',
        '38' => 'Services not provided or authorized by designated (network/primary care) providers',
        '39' => 'Services denied at the time authorization/pre-certification was requested',
        '40' => 'Charges do not meet qualifications for emergent/urgent care',
        '42' => 'Charges exceed our fee schedule or maximum allowable amount',
        '43' => 'Gramm-Rudman reduction',
        '44' => 'Prompt-pay discount',
        '45' => 'Charges exceed your contracted/ legislated fee arrangement',
        '49' => 'These are non-covered services because this is a routine exam or screening procedure done in conjunction with a routine exam',
        '50' => 'These are non-covered services because this is not deemed a "medical necessity" by the payer',
        '51' => 'These are non-covered services because this is a pre-existing condition',
        '53' => 'Services by an immediate relative or a member of the same household are not covered',
        '54' => 'Multiple physicians/assistants are not covered in this case ',
        '55' => 'Claim/service denied because procedure/treatment is deemed experimental/investigational by the payer',
        '56' => 'Claim/service denied because procedure/treatment has not been deemed "proven to be effective" by the payer',
        '57' => 'Payment denied/reduced because the payer deems the information submitted does not support this level of service, this many services, this length of service, this dosage, or this days supply',
        '58' => 'Payment adjusted because treatment was deemed by the payer to have been rendered in an inappropriate or invalid place of service',
        '59' => 'Charges are adjusted based on multiple surgery rules or concurrent anesthesia rules',
        '60' => 'Charges for outpatient services with this proximity to inpatient services are not covered',
        '61' => 'Charges adjusted as penalty for failure to obtain second surgical opinion',
        '62' => 'Payment denied/reduced for absence of, or exceeded, pre-certification/authorization',
        '66' => 'Blood Deductible',
        '69' => 'Day outlier amount',
        '70' => 'Cost outlier - Adjustment to compensate for additional costs',
        '74' => 'Indirect Medical Education Adjustment',
        '75' => 'Direct Medical Education Adjustment',
        '76' => 'Disproportionate Share Adjustment',
        '78' => 'Non-Covered days/Room charge adjustment',
        '85' => 'Interest amount',
        '87' => 'Transfer amount',
        '88' => 'Adjustment amount represents collection against receivable created in prior overpayment',
        '89' => 'Professional fees removed from charges',
        '90' => 'Ingredient cost adjustment',
        '91' => 'Dispensing fee adjustment',
        '94' => 'Processed in Excess of charges',
        '95' => 'Benefits adjusted. Plan procedures not followed',
        '96' => 'Non-covered charge(s)',
        '97' => 'Payment is included in the allowance for another service/procedure',
        '100' => 'Payment made to patient/insured/responsible party',
        '101' => 'Predetermination: anticipated payment upon completion of services or claim adjudication',
        '102' => 'Major Medical Adjustment',
        '103' => 'Provider promotional discount (e.g., Senior citizen discount)',
        '104' => 'Managed care withholding',
        '105' => 'Tax withholding',
        '106' => 'Patient payment option/election not in effect',
        '107' => 'Claim/service denied because the related or qualifying claim/service was not previously paid or identified on this claim',
        '108' => 'Payment adjusted because rent/purchase guidelines were not met',
        '109' => 'Claim not covered by this payer/contractor. You must send the claim to the correct payer/contractor',
        '110' => 'Billing date predates service date',
        '111' => 'Not covered unless the provider accepts assignment',
        '112' => 'Payment adjusted as not furnished directly to the patient and/or not documented',
        '113' => 'Payment denied because service/procedure was provided outside the United States or as a result of war',
        '114' => 'Procedure/product not approved by the Food and Drug Administration',
        '115' => 'Payment adjusted as procedure postponed or canceled',
        '116' => 'Payment denied. The advance indemnification notice signed by the patient did not comply with requirements',
        '117' => 'Payment adjusted because transportation is only covered to the closest facility that can provide the necessary care',
        '118' => 'Charges reduced for ESRD network support',
        '119' => 'Benefit maximum for this time period or occurrence has been reached',
        '120' => 'Patient is covered by a managed care plan',
        '121' => 'Indemnification adjustment',
        '122' => 'Psychiatric reduction',
        '123' => 'Payer refund due to overpayment',
        '124' => 'Payer refund amount - not our patient',
        '125' => 'Payment adjusted due to a submission/billing error(s). Additional information is supplied using the remittance advice remarks codes whenever appropriate',
        '126' => 'Deductible -- Major Medical',
        '127' => 'Coinsurance -- Major Medical',
        '128' => 'Newborns services are covered in the mothers Allowance',
        '129' => 'Payment denied - Prior processing information appears incorrect',
        '130' => 'Claim submission fee',
        '131' => 'Claim specific negotiated discount',
        '132' => 'Prearranged demonstration project adjustment',
        '133' => 'The disposition of this claim/service is pending further review',
        '134' => 'Technical fees removed from charges',
        '135' => 'Claim denied. Interim bills cannot be processed',
        '136' => 'Claim Adjusted. Plan procedures of a prior payer were not followed',
        '137' => 'Payment/Reduction for Regulatory Surcharges, Assessments, Allowances or Health Related Taxes',
        '138' => 'Claim/service denied. Appeal procedures not followed or time limits not met',
        '139' => 'Contracted funding agreement - Subscriber is employed by the provider of services',
        '140' => 'Patient/Insured health identification number and name do not match',
        '141' => 'Claim adjustment because the claim spans eligible and ineligible periods of coverage',
        '142' => 'Claim adjusted by the monthly Medicaid patient liability amount',
        '143' => 'Portion of payment deferred',
        '144' => 'Incentive adjustment, e.g. preferred product/service',
        '145' => 'Premium payment withholding',
        '146' => 'Payment denied because the diagnosis was invalid for the date(s) of service reported',
        '147' => 'Provider contracted/negotiated rate expired or not on file',
        '148' => 'Claim/service rejected at this time because information from another provider was not provided or was insufficient/incomplete',
        '149' => 'Lifetime benefit maximum has been reached for this service/benefit category',
        '150' => 'Payment adjusted because the payer deems the information submitted does not support this level of service',
        '151' => 'Payment adjusted because the payer deems the information submitted does not support this many services',
        '152' => 'Payment adjusted because the payer deems the information submitted does not support this length of service',
        '153' => 'Payment adjusted because the payer deems the information submitted does not support this dosage',
        '154' => 'Payment adjusted because the payer deems the information submitted does not support this days supply',
        '155' => 'This claim is denied because the patient refused the service/procedure',
        '156' => 'Flexible spending account payments',
        '157' => 'Payment denied/reduced because service/procedure was provided as a result of an act of war',
        '158' => 'Payment denied/reduced because the service/procedure was provided outside of the United States',
        '159' => 'Payment denied/reduced because the service/procedure was provided as a result of terrorism',
        '160' => 'Payment denied/reduced because injury/illness was the result of an activity that is a benefit exclusion',
        '161' => 'Provider performance bonus',
        '162' => 'State-mandated Requirement for Property and Casualty, see Claim Payment Remarks Code for specific explanation',
        '163' => 'Claim/Service adjusted because the attachment referenced on the claim was not received',
        '164' => 'Claim/Service adjusted because the attachment referenced on the claim was not received in a timely fashion',
        '165' => 'Payment denied /reduced for absence of, or exceeded referral',
        '166' => 'These services were submitted after this payers responsibility for processing claims under this plan ended',
        '167' => 'This (these) diagnosis(es) is (are) not covered',
        '168' => 'Payment denied as Service(s) have been considered under the patients medical plan. Benefits are not available under this dental plan',
        '169' => 'Payment adjusted because an alternate benefit has been provided',
        '170' => 'Payment is denied when performed/billed by this type of provider',
        '171' => 'Payment is denied when performed/billed by this type of provider in this type of facility',
        '172' => 'Payment is adjusted when performed/billed by a provider of this specialty',
        '173' => 'Payment adjusted because this service was not prescribed by a physician',
        '174' => 'Payment denied because this service was not prescribed prior to delivery',
        '175' => 'Payment denied because the prescription is incomplete',
        '176' => 'Payment denied because the prescription is not current',
        '177' => 'Payment denied because the patient has not met the required eligibility requirements',
        '178' => 'Payment adjusted because the patient has not met the required spend down requirements',
        '179' => 'Payment adjusted because the patient has not met the required waiting requirements',
        '180' => 'Payment adjusted because the patient has not met the required residency requirements',
        '181' => 'Payment adjusted because this procedure code was invalid on the date of service',
        '182' => 'Payment adjusted because the procedure modifier was invalid on the date of service',
        '183' => 'The referring provider is not eligible to refer the service billed',
        '184' => 'The prescribing/ordering provider is not eligible to prescribe/order the service billed',
        '185' => 'The rendering provider is not eligible to perform the service billed',
        '186' => 'Payment adjusted since the level of care changed',
        '187' => 'Health Savings account payments',
        '188' => 'This product/procedure is only covered when used according to FDA recommendations',
        '189' => '"Not otherwise classified" or "unlisted" procedure code (CPT/HCPCS) was billed when there is a specific procedure code for this procedure/service',
        '190' => 'Payment is included in the allowance for a Skilled Nursing Facility (SNF) qualified stay',
        '191' => 'Claim denied because this is not a work related injury/illness and thus not the liability of the workers’ compensation carrier',
        '192' => 'Non standard adjustment code from paper remittance advice',
        '193' => 'Original payment decision is being maintained. This claim was processed properly the first time',
        '194' => 'Payment adjusted when anesthesia is performed by the operating physician, the assistant surgeon or the attending physician',
        '195' => 'Payment denied/reduced due to a refund issued to an erroneous priority payer for this claim/service',
        'A0' => 'Patient refund amount',
        'A1' => 'Claim denied charges',
        'A2' => 'Contractual adjustment',
        'A4' => 'Medicare Claim PPS Capital Day Outlier Amount',
        'A5' => 'Medicare Claim PPS Capital Cost Outlier Amount',
        'A6' => 'Prior hospitalization or 30 day transfer requirement not met',
        'A7' => 'Presumptive Payment Adjustment',
        'A8' => 'Claim denied; ungroupable DRG',
        'B1' => 'Non-covered visits',
        'B4' => 'Late filing penalty',
        'B5' => 'Payment adjusted because coverage/program guidelines were not met or were exceeded',
        'B7' => 'This provider was not certified/eligible to be paid for this procedure/service on this date of service',
        'B8' => 'Claim/service not covered/reduced because alternative services were available, and should have been utilized',
        'B9' => 'Services not covered because the patient is enrolled in a Hospice',
        'B10' => 'Allowed amount has been reduced because a component of the basic procedure/test was paid. The beneficiary is not liable for more than the charge limit for the basic procedure/test',
        'B11' => 'The claim/service has been transferred to the proper payer/processor for processing. Claim/service not covered by this payer/processor',
        'B12' => 'Services not documented in patients medical records',
        'B13' => 'Previously paid. Payment for this claim/service may have been provided in a previous payment',
        'B14' => 'Payment denied because only one visit or consultation per physician per day is covered',
        'B15' => 'Payment adjusted because this procedure/service is not paid separately',
        'B16' => 'Payment adjusted because "New Patient" qualifications were not met',
        'B18' => 'Payment adjusted because this procedure code and modifier were invalid on the date of service',
        'B20' => 'Payment adjusted because procedure/service was partially or fully furnished by another provider',
        'B22' => 'This payment is adjusted based on the diagnosis',
        'B23' => 'Payment denied because this provider has failed an aspect of a proficiency testing program',
        'D16' => 'Claim lacks prior payer payment information',
        'D17' => 'Claim/Service has invalid non-covered days',
        'D18' => 'Claim/Service has missing diagnosis information',
        'D19' => 'Claim/Service lacks Physician/Operative or other supporting documentation',
        'D20' => 'Claim/Service missing service/product information',
        'D21' => 'This (these) diagnosis(es) is (are) missing or are invalid',
        'W1' => 'Workers Compensation State Fee Schedule Adjustment',
    );

    const remittance_advice_remark_codes = array(
        'M1' => 'X-ray not taken within the past 12 months or near enough to the start of treatment.',
        'M2' => 'Not paid separately when the patient is an inpatient.',
        'M3' => 'Equipment is the same or similar to equipment already being used.',
        'M4' => 'This is the last monthly installment payment for this durable medical equipment.',
        'M5' => 'Monthly rental payments can continue until the earlier of the 15th month from the first rental month, or the month when the equipment is no longer needed.',
        'M6' => 'You must furnish and service this item for as long as the patient continues to need it. We can pay for maintenance and/or servicing for every 6 month period after the end of the 15th paid rental month or the end of the warranty period.',
        'M7' => 'No rental payments after the item is purchased, or after the total of issued rental payments equals the purchase price.',
        'M8' => 'We do not accept blood gas tests results when the test was conducted by a medical supplier or taken while the patient is on oxygen.',
        'M9' => 'This is the tenth rental month. You must offer the patient the choice of changing the rental to a purchase agreement.',
        'M10' => 'Equipment purchases are limited to the first or the tenth month of medical necessity.',
        'M11' => 'DME, orthotics and prosthetics must be billed to the DME carrier who services the patient\'s zip code.',
        'M12' => 'Diagnostic tests performed by a physician must indicate whether purchased services are included on the claim.',
        'M13' => 'Only one initial visit is covered per specialty per medical group.',
        'M14' => 'No separate payment for an injection administered during an office visit, and no payment for a full office visit if the patient only received an injection.',
        'M15' => 'Separately billed services/tests have been bundled as they are considered components of the same procedure. Separate payment is not allowed.',
        'M16' => 'Please see our web site, mailings, or bulletins for more details concerning this policy/procedure/decision.',
        'M17' => 'Payment approved as you did not know, and could not reasonably have been expected to know, that this would not normally have been covered for this patient. In the future, you will be liable for charges for the same service(s) under the same or similar conditions.',
        'M18' => 'Certain services may be approved for home use. Neither a hospital nor a Skilled Nursing Facility (SNF) is considered to be a patient\'s home.',
        'M19' => 'Missing oxygen certification/re-certification.',
        'M20' => 'Missing/incomplete/invalid HCPCS.',
        'M21' => 'Missing/incomplete/invalid place of residence for this service/item provided in a home.',
        'M22' => 'Missing/incomplete/invalid number of miles traveled.',
        'M23' => 'Missing invoice.',
        'M24' => 'Missing/incomplete/invalid number of doses per vial.',
        'M25' => 'Payment has been adjusted because the information furnished does not substantiate the need for this level of service. If you believe the service should have been fully covered as billed, or if you did not know and could not reasonably have been expected to know that we would not pay for this level of service, or if you notified the patient in writing in advance that we would not pay for this level of service and he/she agreed in writing to pay, ask us to review your claim within 120 days of the date of this notice. If you do not request a appeal, we will, upon application from the patient, reimburse him/her for the amount you have collected from him/her in excess of any deductible and coinsurance amounts. We will recover the reimbursement from you as an overpayment.',
        'M26' => 'Payment has been adjusted because the information furnished does not substantiate the need for this level of service. If you have collected any amount from the patient for this level of service /any amount that exceeds the limiting charge for the less extensive service, the law requires you to refund that amount to the patient within 30 days of receiving this notice. The requirements for refund are in 1824(I) of the Social Security Act and 42CFR411.408. The section specifies that physicians who knowingly and willfully fail to make appropriate refunds may be subject to civil monetary penalties and/or exclusion from the program. If you have any questions about this notice, please contact this office.',
        'M27' => 'The patient has been relieved of liability of payment of these items and services under the limitation of liability provision of the law. You, the provider, are ultimately liable for the patient\'s waived charges, including any charges for coinsurance, since the items or services were not reasonable and necessary or constituted custodial care, and you knew or could reasonably have been expected to know, that they were not covered. You may appeal this determination. You may ask for an appeal regarding both the coverage determination and the issue of whether you exercised due care. The appeal request must be filed within 120 days of the date you receive this notice. You must make the request through this office.',
        'M28' => 'This does not qualify for payment under Part B when Part A coverage is exhausted or not otherwise available.',
        'M29' => 'Missing operative report.',
        'M30' => 'Missing pathology report.',
        'M31' => 'Missing radiology report.',
        'M32' => 'This is a conditional payment made pending a decision on this service by the patient\'s primary payer. This payment may be subject to refund upon your receipt of any additional payment for this service from another payer. You must contact this office immediately upon receipt of an additional payment for this service.',
        'M36' => 'This is the 11th rental month. We cannot pay for this until you indicate that the patient has been given the option of changing the rental to a purchase.',
        'M37' => 'Service not covered when the patient is under age 35.',
        'M38' => 'The patient is liable for the charges for this service as you informed the patient in writing before the service was furnished that we would not pay for it, and the patient agreed to pay.',
        'M39' => 'The patient is not liable for payment for this service as the advance notice of non-coverage you provided the patient did not comply with program requirements.',
        'M40' => 'Claim must be assigned and must be filed by the practitioner\'s employer.',
        'M41' => 'We do not pay for this as the patient has no legal obligation to pay for this.',
        'M42' => 'The medical necessity form must be personally signed by the attending physician.',
        'M44' => 'Missing/incomplete/invalid condition code.',
        'M45' => 'Missing/incomplete/invalid occurrence code(s).',
        'M46' => 'Missing/incomplete/invalid occurrence span code(s).',
        'M47' => 'Missing/incomplete/invalid internal or document control number.',
        'M49' => 'Missing/incomplete/invalid value code(s) or amount(s).',
        'M50' => 'Missing/incomplete/invalid revenue code(s).',
        'M51' => 'Missing/incomplete/invalid procedure code(s).',
        'M52' => 'Missing/incomplete/invalid “from” date(s) of service.',
        'M53' => 'Missing/incomplete/invalid days or units of service.',
        'M54' => 'Missing/incomplete/invalid total charges.',
        'M55' => 'We do not pay for self-administered anti-emetic drugs that are not administered with a covered oral anti-cancer drug.',
        'M56' => 'Missing/incomplete/invalid payer identifier.',
        'M59' => 'Missing/incomplete/invalid “to” date(s) of service.',
        'M60' => 'Missing Certificate of Medical Necessity.',
        'M61' => 'We cannot pay for this as the approval period for the FDA clinical trial has expired.',
        'M62' => 'Missing/incomplete/invalid treatment authorization code.',
        'M64' => 'Missing/incomplete/invalid other diagnosis.',
        'M65' => 'One interpreting physician charge can be submitted per claim when a purchased diagnostic test is indicated. Please submit a separate claim for each interpreting physician.',
        'M66' => 'Our records indicate that you billed diagnostic tests subject to price limitations and the procedure code submitted includes a professional component. Only the technical component is subject to price limitations. Please submit the technical and professional components of this service as separate line items.',
        'M67' => 'Missing/incomplete/invalid other procedure code(s).',
        'M69' => 'Paid at the regular rate as you did not submit documentation to justify the modified procedure code.',
        'M70' => 'NDC code submitted for this service was translated to a HCPCS code for processing, but please continue to submit the NDC on future claims for this item.',
        'M71' => 'Total payment reduced due to overlap of tests billed.',
        'M73' => 'The HPSA/Physician Scarcity bonus can only be paid on the professional component of this service. Rebill as separate professional and technical components.',
        'M74' => 'This service does not qualify for a HPSA/Physician Scarcity bonus payment.',
        'M75' => 'Allowed amount adjusted. Multiple automated multichannel tests performed on the same day combined for payment.',
        'M76' => 'Missing/incomplete/invalid diagnosis or condition.',
        'M77' => 'Missing/incomplete/invalid place of service.',
        'M79' => 'Missing/incomplete/invalid charge.',
        'M80' => 'Not covered when performed during the same session/date as a previously processed service for the patient.',
        'M81' => 'You are required to code to the highest level of specificity.',
        'M82' => 'Service is not covered when patient is under age 50.',
        'M83' => 'Service is not covered unless the patient is classified as at high risk.',
        'M84' => 'Medical code sets used must be the codes in effect at the time of service',
        'M85' => 'Subjected to review of physician evaluation and management services.',
        'M86' => 'Service denied because payment already made for same/similar procedure within set time frame.',
        'M87' => 'Claim/service(s) subjected to CFO-CAP prepayment review.',
        'M89' => 'Not covered more than once under age 40.',
        'M90' => 'Not covered more than once in a 12 month period.',
        'M91' => 'Lab procedures with different CLIA certification numbers must be billed on separate claims.',
        'M93' => 'Information supplied supports a break in therapy. A new capped rental period began with delivery of this equipment.',
        'M94' => 'Information supplied does not support a break in therapy. A new capped rental period will not begin.',
        'M95' => 'Services subjected to Home Health Initiative medical review/cost report audit.',
        'M96' => 'The technical component of a service furnished to an inpatient may only be billed by that inpatient facility. You must contact the inpatient facility for technical component reimbursement. If not already billed, you should bill us for the professional component only.',
        'M97' => 'Not paid to practitioner when provided to patient in this place of service. Payment included in the reimbursement issued the facility.',
        'M99' => 'Missing/incomplete/invalid Universal Product Number/Serial Number.',
        'M100' => 'We do not pay for an oral anti-emetic drug that is not administered for use immediately before, at, or within 48 hours of administration of a covered chemotherapy drug.',
        'M102' => 'Service not performed on equipment approved by the FDA for this purpose.',
        'M103' => 'Information supplied supports a break in therapy. However, the medical information we have for this patient does not support the need for this item as billed. We have approved payment for this item at a reduced level, and a new capped rental period will begin with the delivery of this equipment.',
        'M104' => 'Information supplied supports a break in therapy. A new capped rental period will begin with delivery of the equipment. This is the maximum approved under the fee schedule for this item or service.',
        'M105' => 'Information supplied does not support a break in therapy. The medical information we have for this patient does not support the need for this item as billed. We have approved payment for this item at a reduced level, and a new capped rental period will not begin.',
        'M107' => 'Payment reduced as 90-day rolling average hematocrit for ESRD patient exceeded 36.5%.',
        'M109' => 'We have provided you with a bundled payment for a teleconsultation. You must send 25 percent of the teleconsultation payment to the referring practitioner.',
        'M111' => 'We do not pay for chiropractic manipulative treatment when the patient refuses to have an x-ray taken.',
        'M112' => 'The approved amount is based on the maximum allowance for this item under the DMEPOS Competitive Bidding Demonstration.',
        'M113' => 'Our records indicate that this patient began using this service(s) prior to the current round of the DMEPOS Competitive Bidding Demonstration. Therefore, the approved amount is based on the allowance in effect prior to this round of bidding for this item.',
        'M114' => 'This service was processed in accordance with rules and guidelines under the Competitive Bidding Demonstration Project. If you would like more information regarding this project, you may phone 1-888-289-0710.',
        'M115' => 'This item is denied when provided to this patient by a non-demonstration supplier.',
        'M116' => 'Paid under the Competitive Bidding Demonstration project. Project is ending, and future services may not be paid under this project.',
        'M117' => 'Not covered unless submitted via electronic claim.',
        'M118' => 'Letter to follow containing further information.',
        'M119' => 'Missing/incomplete/invalid/ deactivated/withdrawn National Drug Code (NDC).',
        'M121' => 'We pay for this service only when performed with a covered cryosurgical ablation.',
        'M122' => 'Missing/incomplete/invalid level of subluxation.',
        'M123' => 'Missing/incomplete/invalid name, strength, or dosage of the drug furnished.',
        'M124' => 'Missing indication of whether the patient owns the equipment that requires the part or supply.',
        'M125' => 'Missing/incomplete/invalid information on the period of time for which the service/supply/equipment will be needed.',
        'M126' => 'Missing/incomplete/invalid individual lab codes included in the test.',
        'M127' => 'Missing patient medical record for this service.',
        'M129' => 'Missing/incomplete/invalid indicator of x-ray availability for review.',
        'M130' => 'Missing invoice or statement certifying the actual cost of the lens, less discounts, and/or the type of intraocular lens used.',
        'M131' => 'Missing physician financial relationship form.',
        'M132' => 'Missing pacemaker registration form.',
        'M133' => 'Claim did not identify who performed the purchased diagnostic test or the amount you were charged for the test.',
        'M134' => 'Performed by a facility/supplier in which the provider has a financial interest.',
        'M135' => 'Missing/incomplete/invalid plan of treatment.',
        'M136' => 'Missing/incomplete/invalid indication that the service was supervised or evaluated by a physician.',
        'M137' => 'Part B coinsurance under a demonstration project.',
        'M138' => 'Patient identified as a demonstration participant but the patient was not enrolled in the demonstration at the time services were rendered. Coverage is limited to demonstration participants.',
        'M139' => 'Denied services exceed the coverage limit for the demonstration.',
        'M141' => 'Missing physician certified plan of care.',
        'M142' => 'Missing American Diabetes Association Certificate of Recognition.',
        'M143' => 'We have no record that you are licensed to dispensed drugs in the State where located.',
        'M144' => 'Pre-/post-operative care payment is included in the allowance for the surgery/procedure.',
        'MA01' => 'If you do not agree with what we approved for these services, you may appeal our decision. To make sure that we are fair to you, we require another individual that did not process your initial claim to conduct the appeal. However, in order to be eligible for an appeal, you must write to us within 120 days of the date you received this notice, unless you have a good reason for being late.',
        'MA02' => 'If you do not agree with this determination, you have the right to appeal. You must file a written request for an appeal within 180 days of the date you receive this notice. Decisions made by a Quality Improvement Organization (QIO) must be appealed to that QIO within 60 days.',
        'MA03' => 'If you do not agree with the approved amounts and $100 or more is in dispute (less deductible and coinsurance), you may ask for a hearing within six months of the date of this notice. To meet the $100, you may combine amounts on other claims that have been denied, including reopened appeals if you received a revised decision. You must appeal each claim on time.',
        'MA04' => 'Secondary payment cannot be considered without the identity of or payment information from the primary payer. The information was either not reported or was illegible.',
        'MA07' => 'The claim information has also been forwarded to Medicaid for review.',
        'MA08' => 'You should also submit this claim to the patient\'s other insurer for potential payment of supplemental benefits. We did not forward the claim information as the supplemental coverage is not with a Medigap plan, or you do not participate in Medicare.',
        'MA09' => 'Claim submitted as unassigned but processed as assigned. You agreed to accept assignment for all claims.',
        'MA10' => 'The patient\'s payment was in excess of the amount owed. You must refund the overpayment to the patient.',
        'MA12' => 'You have not established that you have the right under the law to bill for services furnished by the person(s) that furnished this (these) service(s).',
        'MA13' => 'You may be subject to penalties if you bill the patient for amounts not reported with the PR (patient responsibility) group code.',
        'MA14' => 'Patient is a member of an employer-sponsored prepaid health plan. Services from outside that health plan are not covered. However, as you were not previously notified of this, we are paying this time. In the future, we will not pay you for non-plan services.',
        'MA15' => 'Your claim has been separated to expedite handling. You will receive a separate notice for the other services reported.',
        'MA16' => 'The patient is covered by the Black Lung Program. Send this claim to the Department of Labor, Federal Black Lung Program, P.O. Box 828, Lanham-Seabrook MD 20703.',
        'MA17' => 'We are the primary payer and have paid at the primary rate. You must contact the patient\'s other insurer to refund any excess it may have paid due to its erroneous primary payment.',
        'MA18' => 'The claim information is also being forwarded to the patient\'s supplemental insurer. Send any questions regarding supplemental benefits to them.',
        'MA19' => 'Information was not sent to the Medigap insurer due to incorrect/invalid information you submitted concerning that insurer. Please verify your information and submit your secondary claim directly to that insurer.',
        'MA20' => 'Skilled Nursing Facility (SNF) stay not covered when care is primarily related to the use of an urethral catheter for convenience or the control of incontinence.',
        'MA21' => 'SSA records indicate mismatch with name and sex.',
        'MA22' => 'Payment of less than $1.00 suppressed.',
        'MA23' => 'Demand bill approved as result of medical review.',
        'MA24' => 'Christian Science Sanitarium/ Skilled Nursing Facility (SNF) bill in the same benefit period.',
        'MA25' => 'A patient may not elect to change a hospice provider more than once in a benefit period.',
        'MA26' => 'Our records indicate that you were previously informed of this rule.',
        'MA27' => 'Missing/incomplete/invalid entitlement number or name shown on the claim.',
        'MA28' => 'Receipt of this notice by a physician or supplier who did not accept assignment is for information only and does not make the physician or supplier a party to the determination. No additional rights to appeal this decision, above those rights already provided for by regulation/instruction, are conferred by receipt of this notice.',
        'MA30' => 'Missing/incomplete/invalid type of bill.',
        'MA31' => 'Missing/incomplete/invalid beginning and ending dates of the period billed.',
        'MA32' => 'Missing/incomplete/invalid number of covered days during the billing period.',
        'MA33' => 'Missing/incomplete/invalid noncovered days during the billing period.',
        'MA34' => 'Missing/incomplete/invalid number of coinsurance days during the billing period.',
        'MA35' => 'Missing/incomplete/invalid number of lifetime reserve days.',
        'MA36' => 'Missing/incomplete/invalid patient name.',
        'MA37' => 'Missing/incomplete/invalid patient\'s address.',
        'MA39' => 'Missing/incomplete/invalid gender.',
        'MA40' => 'Missing/incomplete/invalid admission date.',
        'MA41' => 'Missing/incomplete/invalid admission type.',
        'MA42' => 'Missing/incomplete/invalid admission source.',
        'MA43' => 'Missing/incomplete/invalid patient status.',
        'MA44' => 'No appeal rights. Adjudicative decision based on law.',
        'MA45' => 'As previously advised, a portion or all of your payment is being held in a special account.',
        'MA46' => 'The new information was considered, however, additional payment cannot be issued. Please review the information listed for the explanation.',
        'MA47' => 'Our records show you have opted out of Medicare, agreeing with the patient not to bill Medicare for services/tests/supplies furnished. As result, we cannot pay this claim. The patient is responsible for payment.',
        'MA48' => 'Missing/incomplete/invalid name or address of responsible party or primary payer.',
        'MA50' => 'Missing/incomplete/invalid Investigational Device Exemption number for FDA-approved clinical trial services.',
        'MA53' => 'Missing/incomplete/invalid Competitive Bidding Demonstration Project identification.',
        'MA54' => 'Physician certification or election consent for hospice care not received timely.',
        'MA55' => 'Not covered as patient received medical health care services, automatically revoking his/her election to receive religious non-medical health care services.',
        'MA56' => 'Our records show you have opted out of Medicare, agreeing with the patient not to bill Medicare for services/tests/supplies furnished. As result, we cannot pay this claim. The patient is responsible for payment, but under Federal law, you cannot charge the patient more than the limiting charge amount.',
        'MA57' => 'Patient submitted written request to revoke his/her election for religious non-medical health care services.',
        'MA58' => 'Missing/incomplete/invalid release of information indicator.',
        'MA59' => 'The patient overpaid you for these services. You must issue the patient a refund within 30 days for the difference between his/her payment and the total amount shown as patient responsibility on this notice.',
        'MA60' => 'Missing/incomplete/invalid patient relationship to insured.',
        'MA61' => 'Missing/incomplete/invalid social security number or health insurance claim number.',
        'MA62' => 'Telephone review decision.',
        'MA63' => 'Missing/incomplete/invalid principal diagnosis.',
        'MA64' => 'Our records indicate that we should be the third payer for this claim. We cannot process this claim until we have received payment information from the primary and secondary payers.',
        'MA65' => 'Missing/incomplete/invalid admitting diagnosis.',
        'MA66' => 'Missing/incomplete/invalid principal procedure code.',
        'MA67' => 'Correction to a prior claim.',
        'MA68' => 'We did not crossover this claim because the secondary insurance information on the claim was incomplete. Please supply complete information or use the PLANID of the insurer to assure correct and timely routing of the claim.',
        'MA69' => 'Missing/incomplete/invalid remarks.',
        'MA70' => 'Missing/incomplete/invalid provider representative signature.',
        'MA71' => 'Missing/incomplete/invalid provider representative signature date.',
        'MA72' => 'The patient overpaid you for these assigned services. You must issue the patient a refund within 30 days for the difference between his/her payment to you and the total of the amount shown as patient responsibility and as paid to the patient on this notice.',
        'MA73' => 'Informational remittance associated with a Medicare demonstration. No payment issued under fee-for-service Medicare as patient has elected managed care.',
        'MA74' => 'This payment replaces an earlier payment for this claim that was either lost, damaged or returned.',
        'MA75' => 'Missing/incomplete/invalid patient or authorized representative signature.',
        'MA76' => 'Missing/incomplete/invalid provider identifier for home health agency or hospice when physician is performing care plan oversight services.',
        'MA77' => 'The patient overpaid you. You must issue the patient a refund within 30 days for the difference between the patient’s payment less the total of our and other payer payments and the amount shown as patient responsibility on this notice.',
        'MA79' => 'Billed in excess of interim rate.',
        'MA80' => 'Informational notice. No payment issued for this claim with this notice. Payment issued to the hospital by its intermediary for all services for this encounter under a demonstration project.',
        'MA81' => 'Missing/incomplete/invalid provider/supplier signature.',
        'MA83' => 'Did not indicate whether we are the primary or secondary payer.',
        'MA84' => 'Patient identified as participating in the National Emphysema Treatment Trial but our records indicate that this patient is either not a participant, or has not yet been approved for this phase of the study. Contact Johns Hopkins University, the study coordinator, to resolve if there was a discrepancy.',
        'MA88' => 'Missing/incomplete/invalid insured\'s address and/or telephone number for the primary payer.',
        'MA89' => 'Missing/incomplete/invalid patient\'s relationship to the insured for the primary payer.',
        'MA90' => 'Missing/incomplete/invalid employment status code for the primary insured.',
        'MA91' => 'This determination is the result of the appeal you filed.',
        'MA92' => 'Missing plan information for other insurance.',
        'MA93' => 'Non-PIP (Periodic Interim Payment) claim.',
        'MA94' => 'Did not enter the statement “Attending physician not hospice employee” on the claim form to certify that the rendering physician is not an employee of the hospice.',
        'MA95' => 'De-activate and refer to M51.',
        'MA96' => 'Claim rejected. Coded as a Medicare Managed Care Demonstration but patient is not enrolled in a Medicare managed care plan.',
        'MA97' => 'Missing/incomplete/invalid Medicare Managed Care Demonstration contract number.',
        'MA99' => 'Missing/incomplete/invalid Medigap information.',
        'MA100' => 'Missing/incomplete/invalid date of current illness or symptoms',
        'MA101' => 'A Skilled Nursing Facility (SNF) is responsible for payment of outside providers who furnish these services/supplies to residents.',
        'MA103' => 'Hemophilia Add On.',
        'MA106' => 'PIP (Periodic Interim Payment) claim.',
        'MA107' => 'Paper claim contains more than three separate data items in field 19.',
        'MA108' => 'Paper claim contains more than one data item in field 23.',
        'MA109' => 'Claim processed in accordance with ambulatory surgical guidelines.',
        'MA110' => 'Missing/incomplete/invalid information on whether the diagnostic test(s) were performed by an outside entity or if no purchased tests are included on the claim.',
        'MA111' => 'Missing/incomplete/invalid purchase price of the test(s) and/or the performing laboratory\'s name and address.',
        'MA112' => 'Missing/incomplete/invalid group practice information.',
        'MA113' => 'Incomplete/invalid taxpayer identification number (TIN) submitted by you per the Internal Revenue Service. Your claims cannot be processed without your correct TIN, and you may not bill the patient pending correction of your TIN. There are no appeal rights for unprocessable claims, but you may resubmit this claim after you have notified this office of your correct TIN.',
        'MA114' => 'Missing/incomplete/invalid information on where the services were furnished.',
        'MA115' => 'Missing/incomplete/invalid physical location (name and address, or PIN) where the service(s) were rendered in a Health Professional Shortage Area (HPSA).',
        'MA116' => 'Did not complete the statement "Homebound" on the claim to validate whether laboratory services were performed at home or in an institution.',
        'MA117' => 'This claim has been assessed a $1.00 user fee.',
        'MA118' => 'Coinsurance and/or deductible amounts apply to a claim for services or supplies furnished to a Medicare-eligible veteran through a facility of the Department of Veterans Affairs. No Medicare payment issued.',
        'MA119' => 'Provider level adjustment for late claim filing applies to this claim.',
        'MA120' => 'Missing/incomplete/invalid CLIA certification number.',
        'MA121' => 'Missing/incomplete/invalid x-ray date.',
        'MA122' => 'Missing/incomplete/invalid initial treatment date.',
        'MA123' => 'Your center was not selected to participate in this study, therefore, we cannot pay for these services.',
        'MA125' => 'Per legislation governing this program, payment constitutes payment in full.',
        'MA126' => 'Pancreas transplant not covered unless kidney transplant performed.',
        'MA128' => 'Missing/incomplete/invalid FDA approval number.',
        'MA130' => 'Your claim contains incomplete and/or invalid information, and no appeal rights are afforded because the claim is unprocessable. Please submit a new claim with the complete/correct information.',
        'MA131' => 'Physician already paid for services in conjunction with this demonstration claim. You must have the physician withdraw that claim and refund the payment before we can process your claim.',
        'MA132' => 'Adjustment to the pre-demonstration rate.',
        'MA133' => 'Claim overlaps inpatient stay. Rebill only those services rendered outside the inpatient stay.',
        'MA134' => 'Missing/incomplete/invalid provider number of the facility where the patient resides.',
        'N1' => 'You may appeal this decision in writing within the required time limits following receipt of this notice by following the instructions included in your contract or plan benefit documents.',
        'N2' => 'This allowance has been made in accordance with the most appropriate course of treatment provision of the plan.',
        'N3' => 'Missing consent form.',
        'N4' => 'Missing/incomplete/invalid prior insurance carrier EOB.',
        'N5' => 'EOB received from previous payer. Claim not on file.',
        'N6' => 'Under FEHB law (U.S.C. 8904(b)), we cannot pay more for covered care than the amount Medicare would have allowed if the patient were enrolled in Medicare Part A and/or Medicare Part B.',
        'N7' => 'Processing of this claim/service has included consideration under Major Medical provisions.',
        'N8' => 'Crossover claim denied by previous payer and complete claim data not forwarded. Resubmit this claim to this payer to provide adequate data for adjudication.',
        'N9' => 'Adjustment represents the estimated amount a previous payer may pay.',
        'N10' => 'Claim/service adjusted based on the findings of a review organization/professional consult/manual adjudication/medical or dental advisor.',
        'N11' => 'Denial reversed because of medical review.',
        'N12' => 'Policy provides coverage supplemental to Medicare. As member does not appear to be enrolled in Medicare Part B, the member is responsible for payment of the portion of the charge that would have been covered by Medicare.',
        'N13' => 'Payment based on professional/technical component modifier(s).',
        'N14' => 'Payment based on a contractual amount or agreement, fee schedule, or maximum allowable amount.',
        'N15' => 'Services for a newborn must be billed separately.',
        'N16' => 'Family/member Out-of-Pocket maximum has been met. Payment based on a higher percentage.',
        'N19' => 'Procedure code incidental to primary procedure.',
        'N20' => 'Service not payable with other service rendered on the same date.',
        'N21' => 'Your line item has been separated into multiple lines to expedite handling.',
        'N22' => 'This procedure code was added/changed because it more accurately describes the services rendered.',
        'N23' => 'Patient liability may be affected due to coordination of benefits with other carriers and/or maximum benefit provisions.',
        'N24' => 'Missing/incomplete/invalid Electronic Funds Transfer (EFT) banking information.',
        'N25' => 'This company has been contracted by your benefit plan to provide administrative claims payment services only. This company does not assume financial risk or obligation with respect to claims processed on behalf of your benefit plan.',
        'N26' => 'Missing itemized bill.',
        'N27' => 'Missing/incomplete/invalid treatment number.',
        'N28' => 'Consent form requirements not fulfilled.',
        'N29' => 'Missing documentation/orders/notes/summary/report/chart.',
        'N30' => 'Patient ineligible for this service.',
        'N31' => 'Missing/incomplete/invalid prescribing provider identifier.',
        'N32' => 'Claim must be submitted by the provider who rendered the service.',
        'N33' => 'No record of health check prior to initiation of treatment.',
        'N34' => 'Incorrect claim form/format for this service.',
        'N35' => 'Program integrity/utilization review decision.',
        'N36' => 'Claim must meet primary payer’s processing requirements before we can consider payment.',
        'N37' => 'Missing/incomplete/invalid tooth number/letter.',
        'N39' => 'Procedure code is not compatible with tooth number/letter.',
        'N40' => 'Missing x-ray.',
        'N42' => 'No record of mental health assessment.',
        'N43' => 'Bed hold or leave days exceeded.',
        'N45' => 'Payment based on authorized amount.',
        'N46' => 'Missing/incomplete/invalid admission hour.',
        'N47' => 'Claim conflicts with another inpatient stay.',
        'N48' => 'Claim information does not agree with information received from other insurance carrier.',
        'N49' => 'Court ordered coverage information needs validation.',
        'N50' => 'Missing/incomplete/invalid discharge information.',
        'N51' => 'Electronic interchange agreement not on file for provider/submitter.',
        'N52' => 'Patient not enrolled in the billing provider\'s managed care plan on the date of service.',
        'N53' => 'Missing/incomplete/invalid point of pick-up address.',
        'N54' => 'Claim information is inconsistent with pre-certified/authorized services.',
        'N55' => 'Procedures for billing with group/referring/performing providers were not followed.',
        'N56' => 'Procedure code billed is not correct/valid for the services billed or the date of service billed.',
        'N57' => 'Missing/incomplete/invalid prescribing date.',
        'N58' => 'Missing/incomplete/invalid patient liability amount.',
        'N59' => 'Please refer to your provider manual for additional program and provider information.',
        'N61' => 'Rebill services on separate claims.',
        'N62' => 'Inpatient admission spans multiple rate periods. Resubmit separate claims.',
        'N63' => 'Rebill services on separate claim lines.',
        'N64' => 'The “from” and “to” dates must be different.',
        'N65' => 'Procedure code or procedure rate count cannot be determined, or was not on file, for the date of service/provider.',
        'N67' => 'Professional provider services not paid separately. Included in facility payment under a demonstration project. Apply to that facility for payment, or resubmit your claim if: the facility notifies you the patient was excluded from this demonstration; or if you furnished these services in another location on the date of the patient’s admission or discharge from a demonstration hospital. If services were furnished in a facility not involved in the demonstration on the same date the patient was discharged from or admitted to a demonstration facility, you must report the provider ID number for the non-demonstration facility on the new claim.',
        'N68' => 'Prior payment being cancelled as we were subsequently notified this patient was covered by a demonstration project in this site of service. Professional services were included in the payment made to the facility. You must contact the facility for your payment. Prior payment made to you by the patient or another insurer for this claim must be refunded to the payer within 30 days.',
        'N69' => 'PPS (Prospective Payment System) code changed by claims processing system. Insufficient visits or therapies.',
        'N70' => 'Home health consolidated billing and payment applies.',
        'N71' => 'Your unassigned claim for a drug or biological, clinical diagnostic laboratory services or ambulance service was processed as an assigned claim. You are required by law to accept assignment for these types of claims.',
        'N72' => 'PPS (Prospective Payment System) code changed by medical reviewers. Not supported by clinical records.',
        'N74' => 'Resubmit with multiple claims, each claim covering services provided in only one calendar month.',
        'N75' => 'Missing/incomplete/invalid tooth surface information.',
        'N76' => 'Missing/incomplete/invalid number of riders.',
        'N77' => 'Missing/incomplete/invalid designated provider number.',
        'N78' => 'The necessary components of the child and teen checkup (EPSDT) were not completed.',
        'N79' => 'Service billed is not compatible with patient location information.',
        'N80' => 'Missing/incomplete/invalid prenatal screening information.',
        'N81' => 'Procedure billed is not compatible with tooth surface code.',
        'N82' => 'Provider must accept insurance payment as payment in full when a third party payer contract specifies full reimbursement.',
        'N83' => 'No appeal rights. Adjudicative decision based on the provisions of a demonstration project.',
        'N84' => 'Further installment payments forthcoming.',
        'N85' => 'Final installment payment.',
        'N86' => 'A failed trial of pelvic muscle exercise training is required in order for biofeedback training for the treatment of urinary incontinence to be covered.',
        'N87' => 'Home use of biofeedback therapy is not covered.',
        'N88' => 'This payment is being made conditionally. An HHA episode of care notice has been filed for this patient. When a patient is treated under a HHA episode of care, consolidated billing requires that certain therapy services and supplies, such as this, be included in the HHA\'s payment. This payment will need to be recouped from you if we establish that the patient is concurrently receiving treatment under a HHA episode of care.',
        'N89' => 'Payment information for this claim has been forwarded to more than one other payer, but format limitations permit only one of the secondary payers to be identified in this remittance advice.',
        'N90' => 'Covered only when performed by the attending physician.',
        'N91' => 'Services not included in the appeal review.',
        'N92' => 'This facility is not certified for digital mammography.',
        'N93' => 'A separate claim must be submitted for each place of service. Services furnished at multiple sites may not be billed in the same claim.',
        'N94' => 'Claim/Service denied because a more specific taxonomy code is required for adjudication.',
        'N95' => 'This provider type/provider specialty may not bill this service.',
        'N96' => 'Patient must be refractory to conventional therapy (documented behavioral, pharmacologic and/or surgical corrective therapy) and be an appropriate surgical candidate such that implantation with anesthesia can occur.',
        'N97' => 'Patients with stress incontinence, urinary obstruction, and specific neurologic diseases (e.g., diabetes with peripheral nerve involvement) which are associated with secondary manifestations of the above three indications are excluded.',
        'N98' => 'Patient must have had a successful test stimulation in order to support subsequent implantation. Before a patient is eligible for permanent implantation, he/she must demonstrate a 50 percent or greater improvement through test stimulation. Improvement is measured through voiding diaries.',
        'N99' => 'Patient must be able to demonstrate adequate ability to record voiding diary data such that clinical results of the implant procedure can be properly evaluated.',
        'N100' => 'PPS (Prospect Payment System) code corrected during adjudication.',
        'N102' => 'This claim has been denied without reviewing the medical record because the requested records were not received or were not received timely.',
        'N103' => 'Social Security records indicate that this patient was a prisoner when the service was rendered. This payer does not cover items and services furnished to an individual while they are in State or local custody under a penal authority, unless under State or local law, the individual is personally liable for the cost of his or her health care while incarcerated and the State or local government pursues such debt in the same way and with the same vigor as any other debt.',
        'N104' => 'This claim/service is not payable under our claims jurisdiction area. You can identify the correct Medicare contractor to process this claim/service through the CMS website at www.cms.hhs.gov.',
        'N105' => 'This is a misdirected claim/service for an RRB beneficiary. Submit paper claims to the RRB carrier: Palmetto GBA, P.O. Box 10066, Augusta, GA 30999. Call 866-749-4301 for RRB EDI information for electronic claims processing.',
        'N106' => 'Payment for services furnished to Skilled Nursing Facility (SNF) inpatients (except for excluded services) can only be made to the SNF. You must request payment from the SNF rather than the patient for this service.',
        'N107' => 'Services furnished to Skilled Nursing Facility (SNF) inpatients must be billed on the inpatient claim. They cannot be billed separately as outpatient services.',
        'N108' => 'Missing/incomplete/invalid upgrade information.',
        'N109' => 'This claim was chosen for complex review and was denied after reviewing the medical records.',
        'N110' => 'This facility is not certified for film mammography.',
        'N111' => 'No appeal right except duplicate claim/service issue. This service was included in a claim that has been previously billed and adjudicated.',
        'N112' => 'This claim is excluded from your electronic remittance advice.',
        'N113' => 'Only one initial visit is covered per physician, group practice or provider.',
        'N114' => 'During the transition to the Ambulance Fee Schedule, payment is based on the lesser of a blended amount calculated using a percentage of the reasonable charge/cost and fee schedule amounts, or the submitted charge for the service. You will be notified yearly what the percentages for the blended payment calculation will be.',
        'N115' => 'This decision was based on a local medical review policy (LMRP) or Local Coverage Determination (LCD).An LMRP/LCD provides a guide to assist in determining whether a particular item or service is covered. A copy of this policy is available at http://www.cms.hhs.gov/mcd, or if you do not have web access, you may contact the contractor to request a copy of the LMRP/LCD.',
        'N116' => 'This payment is being made conditionally because the service was provided in the home, and it is possible that the patient is under a home health episode of care. When a patient is treated under a home health episode of care, consolidated billing requires that certain therapy services and supplies, such as this, be included in the home health agency’s (HHA’s) payment. This payment will need to be recouped from you if we establish that the patient is concurrently receiving treatment under an HHA episode of care.',
        'N117' => 'This service is paid only once in a patient’s lifetime.',
        'N118' => 'This service is not paid if billed more than once every 28 days.',
        'N119' => 'This service is not paid if billed once every 28 days, and the patient has spent 5 or more consecutive days in any inpatient or Skilled /nursing Facility (SNF) within those 28 days.',
        'N120' => 'Payment is subject to home health prospective payment system partial episode payment adjustment. Patient was transferred/discharged/readmitted during payment episode.',
        'N121' => 'Medicare Part B does not pay for items or services provided by this type of practitioner for beneficiaries in a Medicare Part A covered Skilled Nursing Facility (SNF) stay.',
        'N122' => 'Add-on code cannot be billed by itself.',
        'N123' => 'This is a split service and represents a portion of the units from the originally submitted service.',
        'N124' => 'Payment has been denied for the/made only for a less extensive service/item because the information furnished does not substantiate the need for the (more extensive) service/item. The patient is liable for the charges for this service/item as you informed the patient in writing before the service/item was furnished that we would not pay for it, and the patient agreed to pay.',
        'N125' => 'Payment has been (denied for the/made only for a less extensive) service/item because the information furnished does not substantiate the need for the (more extensive) service/item. If you have collected any amount from the patient, you must refund that amount to the patient within 30 days of receiving this notice. The requirements for a refund are in §1834(a)(18) of the Social Security Act (and in §§1834(j)(4) and 1879(h) by cross-reference to §1834(a)(18)). Section 1834(a)(18)(B) specifies that suppliers which knowingly and willfully fail to make appropriate refunds may be subject to civil money penalties and/or exclusion from the Medicare program. If you have any questions about this notice, please contact this office.',
        'N126' => 'Social Security Records indicate that this individual has been deported. This payer does not cover items and services furnished to individuals who have been deported.',
        'N127' => 'This is a misdirected claim/service for a United Mine Workers of America (UMWA) beneficiary. Please submit claims to them.',
        'N128' => 'This amount represents the prior to coverage portion of the allowance.',
        'N129' => 'This amount represents the dollar amount not eligible due to the patient\'s age.',
        'N130' => 'Consult plan benefit documents for information about restrictions for this service.',
        'N131' => 'Total payments under multiple contracts cannot exceed the allowance for this service.',
        'N132' => 'Payments will cease for services rendered by this US Government debarred or excluded provider after the 30 day grace period as previously notified.',
        'N133' => 'Services for predetermination and services requesting payment are being processed separately.',
        'N134' => 'This represents your scheduled payment for this service. If treatment has been discontinued, please contact Customer Service.',
        'N135' => 'Record fees are the patient\'s responsibility and limited to the specified co-payment.',
        'N136' => 'To obtain information on the process to file an appeal in Arizona, call the Department\'s Consumer Assistance Office at (602) 912-8444 or (800) 325-2548.',
        'N137' => 'The provider acting on the Member\'s behalf, may file an appeal with the Payer. The provider, acting on the Member\'s behalf, may file a complaint with the State Insurance Regulatory Authority without first filing an appeal, if the coverage decision involves an urgent condition for which care has not been rendered. The address may be obtained from the State Insurance Regulatory Authority.',
        'N138' => 'In the event you disagree with the Dental Advisor\'s opinion and have additional information relative to the case, you may submit radiographs to the Dental Advisor Unit at the subscriber\'s dental insurance carrier for a second Independent Dental Advisor Review.',
        'N139' => 'Under the Code of Federal Regulations, Chapter 32, Section 199.13 a non-participating provider is not an appropriate appealing party. Therefore, if you disagree with the Dental Advisor\'s opinion, you may appeal the determination if appointed in writing, by the beneficiary, to act as his/her representative. Should you be appointed as a representative, submit a copy of this letter, a signed statement explaining the matter in which you disagree, and any radiographs and relevant information to the subscriber\'s Dental insurance carrier within 90 days from the date of this letter.',
        'N140' => 'You have not been designated as an authorized OCONUS provider therefore are not considered an appropriate appealing party. If the beneficiary has appointed you, in writing, to act as his/her representative and you disagree with the Dental Advisor\'s opinion, you may appeal by submitting a copy of this letter, a signed statement explaining the matter in which you disagree, and any relevant information to the subscriber\'s Dental insurance carrier within 90 days from the date of this letter.',
        'N141' => 'The patient was not residing in a long-term care facility during all or part of the service dates billed.',
        'N142' => 'The original claim was denied. Resubmit a new claim, not a replacement claim.',
        'N143' => 'The patient was not in a hospice program during all or part of the service dates billed.',
        'N144' => 'The rate changed during the dates of service billed.',
        'N146' => 'Missing screening document.',
        'N147' => 'Long term care case mix or per diem rate cannot be determined because the patient ID number is missing, incomplete, or invalid on the assignment request.',
        'N148' => 'Missing/incomplete/invalid date of last menstrual period.',
        'N149' => 'Rebill all applicable services on a single claim.',
        'N150' => 'Missing/incomplete/invalid model number.',
        'N151' => 'Telephone contact services will not be paid until the face-to-face contact requirement has been met.',
        'N152' => 'Missing/incomplete/invalid replacement claim information.',
        'N153' => 'Missing/incomplete/invalid room and board rate.',
        'N154' => 'This payment was delayed for correction of provider\'s mailing address.',
        'N155' => 'Our records do not indicate that other insurance is on file. Please submit other insurance information for our records.',
        'N156' => 'The patient is responsible for the difference between the approved treatment and the elective treatment.',
        'N157' => 'Transportation to/from this destination is not covered.',
        'N158' => 'Transportation in a vehicle other than an ambulance is not covered.',
        'N159' => 'Payment denied/reduced because mileage is not covered when the patient is not in the ambulance.',
        'N160' => 'The patient must choose an option before a payment can be made for this procedure/ equipment/ supply/ service.',
        'N161' => 'This drug/service/supply is covered only when the associated service is covered.',
        'N162' => 'This is an alert. Although your claim was paid, you have billed for a test/specialty not included in your Laboratory Certification. Your failure to correct the laboratory certification information will result in a denial of payment in the near future.',
        'N163' => 'Medical record does not support code billed per the code definition.',
        'N167' => 'Charges exceed the post-transplant coverage limit.',
        'N170' => 'A new/revised/renewed certificate of medical necessity is needed.',
        'N171' => 'Payment for repair or replacement is not covered or has exceeded the purchase price.',
        'N172' => 'The patient is not liable for the denied/adjusted charge(s) for receiving any updated service/item.',
        'N173' => 'No qualifying hospital stay dates were provided for this episode of care.',
        'N174' => 'This is not a covered service/procedure/ equipment/bed, however patient liability is limited to amounts shown in the adjustments under group "PR".',
        'N175' => 'Missing Review Organization Approval.',
        'N176' => 'Services provided aboard a ship are covered only when the ship is of United States registry and is in United States waters. In addition, a doctor licensed to practice in the United States must provide the service.',
        'N177' => 'We did not send this claim to patient’s other insurer. They have indicated no additional payment can be made.',
        'N178' => 'Missing pre-operative photos or visual field results.',
        'N179' => 'Additional information has been requested from the member. The charges will be reconsidered upon receipt of that information.',
        'N180' => 'This item or service does not meet the criteria for the category under which it was billed.',
        'N181' => 'Additional information has been requested from another provider involved in the care of this member. The charges will be reconsidered upon receipt of that information.',
        'N182' => 'This claim/service must be billed according to the schedule for this plan.',
        'N183' => 'This is a predetermination advisory message, when this service is submitted for payment additional documentation as specified in plan documents will be required to process benefits.',
        'N184' => 'Rebill technical and professional components separately.',
        'N185' => 'Do not resubmit this claim/service.',
        'N186' => 'Non-Availability Statement (NAS) required for this service. Contact the nearest Military Treatment Facility (MTF) for assistance.',
        'N187' => 'You may request a review in writing within the required time limits following receipt of this notice by following the instructions included in your contract or plan benefit documents.',
        'N188' => 'The approved level of care does not match the procedure code submitted.',
        'N189' => 'This service has been paid as a one-time exception to the plan\'s benefit restrictions.',
        'N190' => 'Missing contract indicator.',
        'N191' => 'The provider must update insurance information directly with payer.',
        'N192' => 'Patient is a Medicaid/Qualified Medicare Beneficiary.',
        'N193' => 'Specific federal/state/local program may cover this service through another payer.',
        'N194' => 'Technical component not paid if provider does not own the equipment used.',
        'N195' => 'The technical component must be billed separately.',
        'N196' => 'Patient eligible to apply for other coverage which may be primary.',
        'N197' => 'The subscriber must update insurance information directly with payer.',
        'N198' => 'Rendering provider must be affiliated with the pay-to provider.',
        'N199' => 'Additional payment approved based on payer-initiated review/audit.',
        'N200' => 'The professional component must be billed separately.',
        'N201' => 'A mental health facility is responsible for payment of outside providers who furnish these services/supplies to residents.',
        'N202' => 'Additional information/explanation will be sent separately',
        'N203' => 'Missing/incomplete/invalid anesthesia time/units',
        'N204' => 'Services under review for possible pre-existing condition. Send medical records for prior 12 months',
        'N205' => 'Information provided was illegible',
        'N206' => 'The supporting documentation does not match the claim',
        'N207' => 'Missing/incomplete/invalid weight.',
        'N208' => 'Missing/incomplete/invalid DRG code',
        'N209' => 'Missing/invalid/incomplete taxpayer identification number (TIN)',
        'N210' => 'You may appeal this decision',
        'N211' => 'You may not appeal this decision',
        'N212' => 'Charges processed under a Point of Service benefit',
        'N213' => 'Missing/incomplete/invalid facility/discrete unit DRG/DRG exempt status information',
        'N214' => 'Missing/incomplete/invalid history of the related initial surgical procedure(s)',
        'N215' => 'A payer providing supplemental or secondary coverage shall not require a claims determination for this service from a primary payer as a condition of making its own claims determination.',
        'N216' => 'Patient is not enrolled in this portion of our benefit package',
        'N217' => 'We pay only one site of service per provider per claim',
        'N218' => 'You must furnish and service this item for as long as the patient continues to need it. We can pay for maintenance and/or servicing for the time period specified in the contract or coverage manual.',
        'N219' => 'Payment based on previous payer\'s allowed amount.',
        'N220' => 'See the payer\'s web site or contact the payer\'s Customer Service department to obtain forms and instructions for filing a provider dispute.',
        'N221' => 'Missing Admitting History and Physical report.',
        'N222' => 'Incomplete/invalid Admitting History and Physical report.',
        'N223' => 'Missing documentation of benefit to the patient during initial treatment period.',
        'N224' => 'Incomplete/invalid documentation of benefit to the patient during initial treatment period.',
        'N225' => 'Incomplete/invalid documentation/orders/notes/summary/report/chart.',
        'N226' => 'Incomplete/invalid American Diabetes Association Certificate of Recognition.',
        'N227' => 'Incomplete/invalid Certificate of Medical Necessity.',
        'N228' => 'Incomplete/invalid consent form.',
        'N229' => 'Incomplete/invalid contract indicator.',
        'N230' => 'Incomplete/invalid indication of whether the patient owns the equipment that requires the part or supply.',
        'N231' => 'Incomplete/invalid invoice or statement certifying the actual cost of the lens, less discounts, and/or the type of intraocular lens used.',
        'N232' => 'Incomplete/invalid itemized bill.',
        'N233' => 'Incomplete/invalid operative report.',
        'N234' => 'Incomplete/invalid oxygen certification/re-certification.',
        'N235' => 'Incomplete/invalid pacemaker registration form.',
        'N236' => 'Incomplete/invalid pathology report.',
        'N237' => 'Incomplete/invalid patient medical record for this service.',
        'N238' => 'Incomplete/invalid physician certified plan of care',
        'N239' => 'Incomplete/invalid physician financial relationship form.',
        'N240' => 'Incomplete/invalid radiology report.',
        'N241' => 'Incomplete/invalid Review Organization Approval.',
        'N242' => 'Incomplete/invalid x-ray.',
        'N243' => 'Incomplete/invalid/not approved screening document.',
        'N244' => 'Incomplete/invalid pre-operative photos/visual field results.',
        'N245' => 'Incomplete/invalid plan information for other insurance',
        'N246' => 'State regulated patient payment limitations apply to this service.',
        'N247' => 'Missing/incomplete/invalid assistant surgeon taxonomy.',
        'N248' => 'Missing/incomplete/invalid assistant surgeon name.',
        'N249' => 'Missing/incomplete/invalid assistant surgeon primary identifier.',
        'N250' => 'Missing/incomplete/invalid assistant surgeon secondary identifier.',
        'N251' => 'Missing/incomplete/invalid attending provider taxonomy.',
        'N252' => 'Missing/incomplete/invalid attending provider name.',
        'N253' => 'Missing/incomplete/invalid attending provider primary identifier.',
        'N254' => 'Missing/incomplete/invalid attending provider secondary identifier.',
        'N255' => 'Missing/incomplete/invalid billing provider taxonomy.',
        'N256' => 'Missing/incomplete/invalid billing provider/supplier name.',
        'N257' => 'Missing/incomplete/invalid billing provider/supplier primary identifier.',
        'N258' => 'Missing/incomplete/invalid billing provider/supplier address.',
        'N259' => 'Missing/incomplete/invalid billing provider/supplier secondary identifier.',
        'N260' => 'Missing/incomplete/invalid billing provider/supplier contact information.',
        'N261' => 'Missing/incomplete/invalid operating provider name.',
        'N262' => 'Missing/incomplete/invalid operating provider primary identifier.',
        'N263' => 'Missing/incomplete/invalid operating provider secondary identifier.',
        'N264' => 'Missing/incomplete/invalid ordering provider name.',
        'N265' => 'Missing/incomplete/invalid ordering provider primary identifier.',
        'N266' => 'Missing/incomplete/invalid ordering provider address.',
        'N267' => 'Missing/incomplete/invalid ordering provider secondary identifier.',
        'N268' => 'Missing/incomplete/invalid ordering provider contact information.',
        'N269' => 'Missing/incomplete/invalid other provider name.',
        'N270' => 'Missing/incomplete/invalid other provider primary identifier.',
        'N271' => 'Missing/incomplete/invalid other provider secondary identifier.',
        'N272' => 'Missing/incomplete/invalid other payer attending provider identifier.',
        'N273' => 'Missing/incomplete/invalid other payer operating provider identifier.',
        'N274' => 'Missing/incomplete/invalid other payer other provider identifier.',
        'N275' => 'Missing/incomplete/invalid other payer purchased service provider identifier.',
        'N276' => 'Missing/incomplete/invalid other payer referring provider identifier.',
        'N277' => 'Missing/incomplete/invalid other payer rendering provider identifier.',
        'N278' => 'Missing/incomplete/invalid other payer service facility provider identifier.',
        'N279' => 'Missing/incomplete/invalid pay-to provider name.',
        'N280' => 'Missing/incomplete/invalid pay-to provider primary identifier.',
        'N281' => 'Missing/incomplete/invalid pay-to provider address.',
        'N282' => 'Missing/incomplete/invalid pay-to provider secondary identifier.',
        'N283' => 'Missing/incomplete/invalid purchased service provider identifier.',
        'N284' => 'Missing/incomplete/invalid referring provider taxonomy.',
        'N285' => 'Missing/incomplete/invalid referring provider name.',
        'N286' => 'Missing/incomplete/invalid referring provider primary identifier.',
        'N287' => 'Missing/incomplete/invalid referring provider secondary identifier.',
        'N288' => 'Missing/incomplete/invalid rendering provider taxonomy.',
        'N289' => 'Missing/incomplete/invalid rendering provider name.',
        'N290' => 'Missing/incomplete/invalid rendering provider primary identifier.',
        'N291' => 'Missing/incomplete/invalid rending provider secondary identifier.',
        'N292' => 'Missing/incomplete/invalid service facility name.',
        'N293' => 'Missing/incomplete/invalid service facility primary identifier.',
        'N294' => 'Missing/incomplete/invalid service facility primary address.',
        'N295' => 'Missing/incomplete/invalid service facility secondary identifier.',
        'N296' => 'Missing/incomplete/invalid supervising provider name.',
        'N297' => 'Missing/incomplete/invalid supervising provider primary identifier.',
        'N298' => 'Missing/incomplete/invalid supervising provider secondary identifier.',
        'N299' => 'Missing/incomplete/invalid occurrence date(s).',
        'N300' => 'Missing/incomplete/invalid occurrence span date(s).',
        'N301' => 'Missing/incomplete/invalid procedure date(s).',
        'N302' => 'Missing/incomplete/invalid other procedure date(s).',
        'N303' => 'Missing/incomplete/invalid principal procedure date.',
        'N304' => 'Missing/incomplete/invalid dispensed date.',
        'N305' => 'Missing/incomplete/invalid accident date.',
        'N306' => 'Missing/incomplete/invalid acute manifestation date.',
        'N307' => 'Missing/incomplete/invalid adjudication or payment date.',
        'N308' => 'Missing/incomplete/invalid appliance placement date.',
        'N309' => 'Missing/incomplete/invalid assessment date.',
        'N310' => 'Missing/incomplete/invalid assumed or relinquished care date.',
        'N311' => 'Missing/incomplete/invalid authorized to return to work date.',
        'N312' => 'Missing/incomplete/invalid begin therapy date.',
        'N313' => 'Missing/incomplete/invalid certification revision date.',
        'N314' => 'Missing/incomplete/invalid diagnosis date.',
        'N315' => 'Missing/incomplete/invalid disability from date.',
        'N316' => 'Missing/incomplete/invalid disability to date.',
        'N317' => 'Missing/incomplete/invalid discharge hour.',
        'N318' => 'Missing/incomplete/invalid discharge or end of care date.',
        'N319' => 'Missing/incomplete/invalid hearing or vision prescription date.',
        'N320' => 'Missing/incomplete/invalid Home Health Certification Period.',
        'N321' => 'Missing/incomplete/invalid last admission period.',
        'N322' => 'Missing/incomplete/invalid last certification date.',
        'N323' => 'Missing/incomplete/invalid last contact date.',
        'N324' => 'Missing/incomplete/invalid last seen/visit date.',
        'N325' => 'Missing/incomplete/invalid last worked date.',
        'N326' => 'Missing/incomplete/invalide last x-ray date.',
        'N327' => 'Missing/incomplete/invalid other insured birth date.',
        'N328' => 'Missing/incomplete/invalid Oxygen Saturation Test date.',
        'N329' => 'Missing/incomplete/invalid patient birth date.',
        'N330' => 'Missing/incomplete/invalid patient death date.',
        'N331' => 'Missing/incomplete/invalid physician order date.',
        'N332' => 'Missing/incomplete/invalid prior hospital discharge date.',
        'N333' => 'Missing/incomplete/invalid prior placement date.',
        'N334' => 'Missing/incomplete/invalid re-evaluation date',
        'N335' => 'Missing/incomplete/invalid referral date.',
        'N336' => 'Missing/incomplete/invalid replacement date.',
        'N337' => 'Missing/incomplete/invalid secondary diagnosis date.',
        'N338' => 'Missing/incomplete/invalid shipped date.',
        'N339' => 'Missing/incomplete/invalid similar illness or symptom date.',
        'N340' => 'Missing/incomplete/invalid subscriber birth date.',
        'N341' => 'Missing/incomplete/invalid surgery date.',
        'N342' => 'Missing/incomplete/invalid test performed date.',
        'N343' => 'Missing/incomplete/invalid Transcutaneous Electrical Nerve Stimulator (TENS) trial start date.',
        'N344' => 'Missing/incomplete/invalid Transcutaneous Electrical Nerve Stimulator (TENS) trial end date.',
        'N345' => 'Date range not valid with units submitted.',
        'N346' => 'Missing/incomplete/invalid oral cavity designation code.',
        'N347' => 'Your claim for a referred or purchased service cannot be paid because payment has already been made for this same service to another provider by a payment contractor representing the payer.',
        'N348' => 'You chose that this service/supply/drug would be rendered/supplied and billed by a different practitioner/supplier.',
        'N349' => 'The administration method and drug must be reported to adjudicate this service.',
        'N350' => 'Missing/incomplete/invalid description of service for a Not Otherwise Classified (NOC) code or an Unlisted procedure.',
        'N351' => 'Service date outside of the approved treatment plan service dates.',
        'N352' => 'There are no scheduled payments for this service. Submit a claim for each patient visit.',
        'N353' => 'Benefits have been estimated, when the actual services have been rendered, additional payment will be considered based on the submitted claim.',
        'N354' => 'Incomplete/invalid invoice',
        'N355' => 'The law permits exceptions to the refund requirement in two cases: - If you did not know, and could not have reasonably been expected to know, that we would not pay for this service; or - If you notified the patient in writing before providing the service that you believed that we were likely to deny the service, and the patient signed a statement agreeing to pay for the service. If you come within either exception, or if you believe the carrier was wrong in its determination that we do not pay for this service, you should request appeal of this determination within 30 days of the date of this notice. Your request for review should include any additional information necessary to support your position. If you request an appeal within 30 days of receiving this notice, you may delay refunding the amount to the patient until you receive the results of the review. If the review decision is favorable to you, you do not need to make any refund. If, however, the review is unfavorable, the law specifies that you must make the refund within 15 days of receiving the unfavorable review decision. The law also permits you to request an appeal at any time within 120 days of the date you receive this notice. However, an appeal request that is received more than 30 days after the date of this notice, does not permit you to delay making the refund. Regardless of when a review is requested, the patient will be notified that you have requested one, and will receive a copy of the determination. The patient has received a separate notice of this denial decision. The notice advises that he/she may be entitled to a refund of any amounts paid, if you should have known that we would not pay and did not tell him/her. It also instructs the patient to contact our office if he/she does not hear anything about a refund within 30 days',
        'N356' => 'This service is not covered when performed with, or subsequent to, a non-covered service.',
        'N357' => 'Time frame requirements between this service/procedure/supply and a related service/procedure/supply have not been met.',
        'N358' => 'This decision may be reviewed if additional documentation as described in the contract or plan benefit documents is submitted.',
        'N359' => 'Missing/incomplete/invalid height.',
        'N360' => 'Coordination of benefits has not been calculated when estimating benefits for this pre-determination. Submit payment information from the primary payer with the secondary claim.',
        'N361' => 'Charges are adjusted based on multiple diagnostic imaging procedure rules.',
        'N362' => 'The number of Days or Units of Service exceeds our acceptable maximum.',
        'N363' => 'Alert: in the near future we are implementing new policies/procedures that would affect this determination.',
        'N364' => 'According to our agreement, you must waive the deductible and/or coinsurance amounts.',
        'N365' => 'This procedure code is not payable. It is for reporting/information purposes only.',
        'N366' => 'Requested information not provided. The claim will be reopened if the information previously requested is submitted within one year after the date of this denial notice.',
        'N367' => 'The claim information has been forwarded to a Health Savings Account processor for review.',
        'N368' => 'You must appeal the determination of the previously ajudicated claim.',
        'N369' => 'Alert: Although this claim has been processed, it is deficient according to state legislation/regulation.',
    );


    public static function getBillingById($id, $cols = "*")
    {
        return sqlQuery("select $cols from billing where id=? and activity=1 order by date DESC limit 0,1", array($id));
    }

    public static function getBillingByPid($pid, $cols = "*")
    {
        return sqlQuery("select $cols from billing where pid =? and activity=1 order by date DESC limit 0,1", array($pid));
    }

    public static function getBillingByEncounter($pid, $encounter, $cols = "code_type, code, code_text")
    {
        $res = sqlStatement("select $cols from billing where encounter = ? and pid=? and activity=1 order by code_type, date ASC", array($encounter, $pid));

        $all = array();
        for ($iter = 0; $row = sqlFetchArray($res); $iter++) {
            $all[$iter] = $row;
        }

        return $all;
    }

    public static function addBilling(
        $encounter_id,
        $code_type,
        $code,
        $code_text,
        $pid,
        $authorized = "0",
        $provider,
        $modifier = "",
        $units = "",
        $fee = "0.00",
        $ndc_info = '',
        $justify = '',
        $billed = 0,
        $notecodes = '',
        $pricelevel = '',
        $revenue_code = ""
    ) {

        $sql = "INSERT INTO billing (date, encounter, code_type, code, code_text, " .
            "pid, authorized, user, groupname, activity, billed, provider_id, " .
            "modifier, units, fee, ndc_info, justify, notecodes, pricelevel, revenue_code) VALUES (" .
            "NOW(), ?, ?, ?, ?, ?, ?, ?, ?,  1, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        return sqlInsert($sql, array($encounter_id, $code_type, $code, $code_text, $pid, $authorized,
            $_SESSION['authId'], $_SESSION['authProvider'], $billed, $provider, $modifier, $units, $fee,
            $ndc_info, $justify, $notecodes, $pricelevel, $revenue_code));
    }

    public static function authorizeBilling($id, $authorized = "1")
    {
        sqlQuery("update billing set authorized = ? where id = ?", array($authorized, $id));
    }

    public static function deleteBilling($id)
    {
        sqlStatement("update billing set activity = 0 where id = ?", array($id));
    }

    public static function clearBilling($id)
    {
        sqlStatement("update billing set justify = '' where id = ?", array($id));
    }

    // This function supports the Billing page (billing_process.php),
    // and initiation of secondary processing (sl_eob.inc.php).
    // It is called in the following situations:
    //
    // * billing_process.php sets bill_time, bill_process, payer and target on
    //   queueing a claim for processing.  Create claims row.
    // * billing_process.php sets claim status to 2, and payer, on marking a
    //   claim as billed without actually generating any billing.  Create a
    //   claims row.  In this case bill_process will remain at 0 and process_time
    //   and process_file will not be set.
    // * billing_process.php sets bill_process, payer, target and x12 partner
    //   before calling gen_x12_837.  Create a claims row.
    // * billing_process.php sets claim status to 2 (billed), bill_process to 2,
    //   process_time and process_file after calling gen_x12_837.  Claims row
    //   already exists.
    // * billing_process.php sets claim status to 2 (billed) after creating
    //   an electronic batch (hcfa-only with recent changes).  Claims
    //   row already exists.
    // * EOB posting updates claim status to mark a payer as done.  Claims row
    //   already exists.
    // * EOB posting reopens an encounter for billing a secondary payer.  Create
    //   a claims row.
    //
    // $newversion should be passed to us to indicate if a new claims row
    // is to be generated, otherwise one must already exist.  The payer, if
    // passed in for the latter case, must match the existing claim.
    //
    // Currently on the billing page the user can select any of the patient's
    // payers.  That logic will tailor the payer choices to the encounter date.
    //
    public static function updateClaim(
        $newversion,
        $patient_id,
        $encounter_id,
        $payer_id = -1,
        $payer_type = -1,
        $status = -1,
        $bill_process = -1,
        $process_file = '',
        $target = '',
        $partner_id = -1,
        $crossover = 0,
        $submitted_claim = ''
    ) {

        $sqlBindArray = array();
        if (!$newversion) {
            $sql = "SELECT * FROM claims WHERE patient_id = ? AND " .
                "encounter_id = ? AND status > 0 AND status < 4 ";
            array_push($sqlBindArray, $patient_id, $encounter_id);
            if ($payer_id >= 0) {
                $sql .= "AND payer_id = ? ";
                $sqlBindArray[] = $payer_id;
            }

            $sql .= "ORDER BY version DESC LIMIT 1";
            $row = sqlQuery($sql, $sqlBindArray);
            if (!$row) {
                return 0;
            }

            if ($payer_id < 0) {
                $payer_id = $row['payer_id'];
            }

            if ($status < 0) {
                $status = $row['status'];
            }

            if ($bill_process < 0) {
                $bill_process = $row['bill_process'];
            }

            if ($partner_id < 0) {
                $partner_id = $row['x12_partner_id'];
            }

            if (!$process_file) {
                $process_file = $row['process_file'];
            }

            if (!$target) {
                $target = $row['target'];
            }
        }

        $claimset = "";
        $sqlBindClaimset = array();
        $billset = "";
        $sqlBindBillset = array();
        if (empty($payer_id) || $payer_id < 0) {
            $payer_id = 0;
        }

        if ($status == 7) {//$status==7 is the claim denial case.
            $claimset .= ", status = ?";
            $sqlBindClaimset[] = $status;
        } elseif ($status >= 0) {
            $claimset .= ", status = ?";
            $sqlBindClaimset[] = $status;
            if ($status > 1) {
                $billset .= ", billed = 1";
                if ($status == 2) {
                    $billset .= ", bill_date = NOW()";
                }
            } else {
                $billset .= ", billed = 0";
            }
        }

        if ($status == 7) {//$status==7 is the claim denial case.
            $billset .= ", bill_process = ?";
            $sqlBindBillset[] = $status;
        } elseif ($bill_process >= 0) {
            $claimset .= ", bill_process = ?";
            $sqlBindClaimset[] = $bill_process;
            $billset .= ", bill_process = ?";
            $sqlBindBillset[] = $bill_process;
        }

        if ($status == 7) {//$status==7 is the claim denial case.
            $claimset .= ", process_file = ?";//Denial reason code is stored here
            $sqlBindClaimset[] = $process_file;
        } elseif ($process_file) {
            $claimset .= ", process_file = ?, process_time = NOW()";
            $sqlBindClaimset[] = $process_file;
            $billset .= ", process_file = ?, process_date = NOW()";
            $sqlBindBillset[] = $process_file;
        }

        if ($target) {
            $claimset .= ", target = ?";
            $sqlBindClaimset[] = $target;
            $billset .= ", target = ?";
            $sqlBindBillset[] = $target;
        }

        if ($payer_id >= 0) {
            $claimset .= ", payer_id = ?, payer_type = ?";
            $sqlBindClaimset[] = $payer_id;
            $sqlBindClaimset[] = $payer_type;
            $billset .= ", payer_id = ?";
            $sqlBindBillset[] = $payer_id;
        }

        if ($partner_id >= 0) {
            $claimset .= ", x12_partner_id = ?";
            $sqlBindClaimset[] = $partner_id;
            $billset .= ", x12_partner_id = ?";
            $sqlBindBillset[] = $partner_id;
        }

        if ($billset) {
            $billset = substr($billset, 2);
            $sqlBindArray = $sqlBindBillset;
            array_push($sqlBindArray, $encounter_id, $patient_id);
            sqlStatement("UPDATE billing SET $billset WHERE " .
                "encounter = ? AND pid= ? AND activity = 1", $sqlBindArray);
        }

        $claimset .= ", submitted_claim = ?";
        $sqlBindClaimset[] = $submitted_claim;
        // If a new claim version is requested, insert its row.
        //
        if ($newversion) {
            /****
             * $payer_id = ($payer_id < 0) ? $row['payer_id'] : $payer_id;
             * $bill_process = ($bill_process < 0) ? $row['bill_process'] : $bill_process;
             * $process_file = ($process_file) ? $row['process_file'] : $process_file;
             * $target = ($target) ? $row['target'] : $target;
             * $partner_id = ($partner_id < 0) ? $row['x12_partner_id'] : $partner_id;
             * $sql = "INSERT INTO claims SET " .
             * "patient_id = '$patient_id', " .
             * "encounter_id = '$encounter_id', " .
             * "bill_time = UNIX_TIMESTAMP(NOW()), " .
             * "payer_id = '$payer_id', " .
             * "status = '$status', " .
             * "payer_type = '" . $row['payer_type'] . "', " .
             * "bill_process = '$bill_process', " .
             * "process_time = '" . $row['process_time'] . "', " .
             * "process_file = '$process_file', " .
             * "target = '$target', " .
             * "x12_partner_id = '$partner_id'";
             ****/
            sqlBeginTrans();
            $version = sqlQuery("SELECT IFNULL(MAX(version),0) + 1 AS increment FROM claims WHERE patient_id = ? AND encounter_id = ?", array($patient_id, $encounter_id));

            $sqlBindArray = array();
            array_push($sqlBindArray, $patient_id, $encounter_id);
            if ($crossover <> 1) {
                $sql .= "INSERT INTO claims SET " .
                    "patient_id = ?, " .
                    "encounter_id = ?, " .
                    "bill_time = NOW() $claimset ," .
                    "version = ?";
                $sqlBindArray = array_merge($sqlBindArray, $sqlBindClaimset);
                array_push($sqlBindArray, $version['increment']);
            } else {//Claim automatic forward case.startTra
                $sql = "INSERT INTO claims SET " .
                    "patient_id = ?, " .
                    "encounter_id = ?, " .
                    "bill_time = NOW(), status=? ," .
                    "version = ?";
                array_push($sqlBindArray, $status, $version['increment']);
            }

            sqlStatement($sql, $sqlBindArray);
            sqlCommitTrans();
        } // Otherwise update the existing claim row.
        //
        else if ($claimset) {
            $sqlBindArray = $sqlBindClaimset;
            array_push($sqlBindArray, $patient_id, $encounter_id, $row['version']);
            $claimset = substr($claimset, 2);
            sqlStatement("UPDATE claims SET $claimset WHERE " .
                "patient_id = ? AND encounter_id = ? AND " .
                // "payer_id = '" . $row['payer_id'] . "' AND " .
                "version = ?", $sqlBindArray);
        }

        // Whenever a claim is marked billed, update A/R accordingly.
        if ($status == 2) {
            if ($payer_type > 0) {
                sqlStatement("UPDATE form_encounter SET " .
                    "last_level_billed = ? WHERE " .
                    "pid = ? AND encounter = ?", array($payer_type, $patient_id, $encounter_id));
            }
        }

        return 1;
    }

    // Determine if the encounter is billed.  It is considered billed if it
    // has at least one chargeable item, and all of them are billed.
    //
    public static function isEncounterBilled($pid, $encounter)
    {
        $billed = -1; // no chargeable services yet

        $bres = sqlStatement(
            "SELECT " .
            "billing.billed FROM billing, code_types WHERE " .
            "billing.pid = ? AND " .
            "billing.encounter = ? AND " .
            "billing.activity = 1 AND " .
            "code_types.ct_key = billing.code_type AND " .
            "code_types.ct_fee = 1 " .
            "UNION " .
            "SELECT billed FROM drug_sales WHERE " .
            "pid = ? AND " .
            "encounter = ?",
            array($pid, $encounter, $pid, $encounter)
        );

        while ($brow = sqlFetchArray($bres)) {
            if ($brow['billed'] == 0) {
                $billed = 0;
            } else {
                if ($billed < 0) {
                    $billed = 1;
                }
            }
        }

        return $billed > 0;
    }

    // Get the co-pay amount that is effective on the given date.
    // Or if no insurance on that date, return -1.
    //
    public static function getCopay($patient_id, $encdate)
    {
        $tmp = sqlQuery("SELECT provider, copay FROM insurance_data " .
            "WHERE pid = ? AND type = 'primary' " .
            "AND date <= ? ORDER BY date DESC LIMIT 1", array($patient_id, $encdate));
        if ($tmp['provider']) {
            return sprintf('%01.2f', 0 + $tmp['copay']);
        }

        return 0;
    }

    // Get the total co-pay amount paid by the patient for an encounter
    public static function getPatientCopay($patient_id, $encounter)
    {
        $resMoneyGot = sqlStatement(
            "SELECT sum(pay_amount) as PatientPay FROM ar_activity where " .
            "pid = ? and encounter = ? and payer_type=0 and account_code='PCP'",
            array($patient_id, $encounter)
        );
        //new fees screen copay gives account_code='PCP'
        $rowMoneyGot = sqlFetchArray($resMoneyGot);
        $Copay = $rowMoneyGot['PatientPay'];
        return $Copay * -1;
    }

    // Get the "next invoice reference number" from this user's pool of reference numbers.
    //
    public static function getInvoiceRefNumber()
    {
        $trow = sqlQuery(
            "SELECT lo.notes " .
            "FROM users AS u, list_options AS lo " .
            "WHERE u.username = ? AND " .
            "lo.list_id = 'irnpool' AND lo.option_id = u.irnpool AND lo.activity = 1 LIMIT 1",
            array($_SESSION['authUser'])
        );
        return empty($trow['notes']) ? '' : $trow['notes'];
    }

    // Increment the "next invoice reference number" of this user's pool.
    // This identifies the "digits" portion of that number and adds 1 to it.
    // If it contains more than one string of digits, the last is used.
    //
    public static function updateInvoiceRefNumber()
    {
        $irnumber = self::getInvoiceRefNumber();
        // Here "?" specifies a minimal match, to get the most digits possible:
        if (preg_match('/^(.*?)(\d+)(\D*)$/', $irnumber, $matches)) {
            $newdigs = sprintf('%0' . strlen($matches[2]) . 'd', $matches[2] + 1);
            $newnumber = $matches[1] . $newdigs . $matches[3];
            sqlStatement(
                "UPDATE users AS u, list_options AS lo " .
                "SET lo.notes = ? WHERE " .
                "u.username = ? AND " .
                "lo.list_id = 'irnpool' AND lo.option_id = u.irnpool",
                array($newnumber, $_SESSION['authUser'])
            );
        }

        return $irnumber;
    }

    // Common function for voiding a receipt or checkout.  When voiding a checkout you can specify
    // $time as a timestamp (yyyy-mm-dd hh:mm:ss) or 'all'; default is the last checkout.
    //
    public static function doVoid($patient_id, $encounter_id, $purge = false, $time = '')
    {
        $what_voided = $purge ? 'checkout' : 'receipt';
        $date_original = '';
        $adjustments = 0;
        $payments = 0;

        if (!$time) {
            // Get last checkout timestamp.
            $corow = sqlQuery(
                "(SELECT bill_date FROM billing WHERE " .
                "pid = ? AND encounter = ? AND activity = 1 AND bill_date IS NOT NULL) " .
                "UNION " .
                "(SELECT bill_date FROM drug_sales WHERE " .
                "pid = ? AND encounter = ? AND bill_date IS NOT NULL) " .
                "ORDER BY bill_date DESC LIMIT 1",
                array($patient_id, $encounter_id, $patient_id, $encounter_id)
            );
            if (!empty($corow['bill_date'])) {
                $date_original = $corow['bill_date'];
            }
        } else if ($time == 'all') {
            $row = sqlQuery(
                "SELECT SUM(pay_amount) AS payments, " .
                "SUM(adj_amount) AS adjustments FROM ar_activity WHERE " .
                "pid = ? AND encounter = ?",
                array($patient_id, $encounter_id)
            );
            $adjustments = empty($row['adjustments']) ? 0 : $row['adjustments'];
            $payments = empty($row['payments']) ? 0 : $row['payments'];
        } else {
            $date_original = $time;
        }

        // Get its charges and adjustments.
        if ($date_original) {
            $row = sqlQuery(
                "SELECT SUM(pay_amount) AS payments, " .
                "SUM(adj_amount) AS adjustments FROM ar_activity WHERE " .
                "pid = ? AND encounter = ? AND post_time = ?",
                array($patient_id, $encounter_id, $date_original)
            );
            $adjustments = empty($row['adjustments']) ? 0 : $row['adjustments'];
            $payments = empty($row['payments']) ? 0 : $row['payments'];
        }

        // Get old invoice reference number.
        $encrow = sqlQuery(
            "SELECT invoice_refno FROM form_encounter WHERE " .
            "pid = ? AND encounter = ? LIMIT 1",
            array($patient_id, $encounter_id)
        );
        $old_invoice_refno = $encrow['invoice_refno'];
        //
        $usingirnpools = self::getInvoiceRefNumber();
        // If not (undoing a checkout or using IRN pools), nothing is done.
        if ($purge || $usingirnpools) {
            $query = "INSERT INTO voids SET " .
                "patient_id = ?, " .
                "encounter_id = ?, " .
                "what_voided = ?, " .
                "date_voided = NOW(), " .
                "user_id = ?, " .
                "amount1 = ?, " .
                "amount2 = ?, " .
                "other_info = ?";
            $sqlarr = array($patient_id, $encounter_id, $what_voided, $_SESSION['authUserID'], $adjustments,
                $payments, $old_invoice_refno);
            if ($date_original) {
                $query .= ", date_original = ?";
                $sqlarr[] = $date_original;
            }

            sqlStatement($query, $sqlarr);
        }

        if ($purge) {
            // Purge means delete adjustments and payments from the last checkout
            // and re-open the visit.
            if ($date_original) {
                sqlStatement(
                    "DELETE FROM ar_activity WHERE " .
                    "pid = ? AND encounter = ? AND post_time = ?",
                    array($patient_id, $encounter_id, $date_original)
                );
                sqlStatement(
                    "UPDATE billing SET billed = 0, bill_date = NULL WHERE " .
                    "pid = ? AND encounter = ? AND activity = 1 AND " .
                    "bill_date IS NOT NULL AND bill_date = ?",
                    array($patient_id, $encounter_id, $date_original)
                );
                sqlStatement(
                    "update drug_sales SET billed = 0, bill_date = NULL WHERE " .
                    "pid = ? AND encounter = ? AND " .
                    "bill_date IS NOT NULL AND bill_date = ?",
                    array($patient_id, $encounter_id, $date_original)
                );
            } else {
                if ($time == 'all') {
                    sqlStatement(
                        "DELETE FROM ar_activity WHERE " .
                        "pid = ? AND encounter = ?",
                        array($patient_id, $encounter_id)
                    );
                }

                sqlStatement(
                    "UPDATE billing SET billed = 0, bill_date = NULL WHERE " .
                    "pid = ? AND encounter = ? AND activity = 1",
                    array($patient_id, $encounter_id)
                );
                sqlStatement(
                    "update drug_sales SET billed = 0, bill_date = NULL WHERE " .
                    "pid = ? AND encounter = ?",
                    array($patient_id, $encounter_id)
                );
            }

            sqlStatement(
                "UPDATE form_encounter SET last_level_billed = 0, " .
                "last_level_closed = 0, stmt_count = 0, last_stmt_date = NULL " .
                "WHERE pid = ? AND encounter = ?",
                array($patient_id, $encounter_id)
            );
        } else if ($usingirnpools) {
            // Non-purge means just assign a new invoice reference number.
            $new_invoice_refno = self::updateInvoiceRefNumber();
            sqlStatement(
                "UPDATE form_encounter " .
                "SET invoice_refno = ? " .
                "WHERE pid = ? AND encounter = ?",
                array($new_invoice_refno, $patient_id, $encounter_id)
            );
        }
    }
}
