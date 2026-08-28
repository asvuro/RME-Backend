<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('abdomen_examinations') && ! Schema::hasIndex('abdomen_examinations', 'abdomen_examinations_visit_id_index')) {
            Schema::table('abdomen_examinations', function (Blueprint $table) {
                $table->index('visit_id');
            });
        }

        if (Schema::hasTable('anal_examinations') && ! Schema::hasIndex('anal_examinations', 'anal_examinations_visit_id_index')) {
            Schema::table('anal_examinations', function (Blueprint $table) {
                $table->index('visit_id');
            });
        }

        if (Schema::hasTable('back_examinations') && ! Schema::hasIndex('back_examinations', 'back_examinations_visit_id_index')) {
            Schema::table('back_examinations', function (Blueprint $table) {
                $table->index('visit_id');
            });
        }

        if (Schema::hasTable('barthel_index_assessments') && ! Schema::hasIndex('barthel_index_assessments', 'barthel_index_assessments_visit_id_index')) {
            Schema::table('barthel_index_assessments', function (Blueprint $table) {
                $table->index('visit_id');
            });
        }

        if (Schema::hasTable('breast_examinations') && ! Schema::hasIndex('breast_examinations', 'breast_examinations_visit_id_index')) {
            Schema::table('breast_examinations', function (Blueprint $table) {
                $table->index('visit_id');
            });
        }

        if (Schema::hasTable('case_manager_assessments') && ! Schema::hasIndex('case_manager_assessments', 'case_manager_assessments_visit_id_index')) {
            Schema::table('case_manager_assessments', function (Blueprint $table) {
                $table->index('visit_id');
            });
        }

        if (Schema::hasTable('cat_clams_examinations') && ! Schema::hasIndex('cat_clams_examinations', 'cat_clams_examinations_visit_id_index')) {
            Schema::table('cat_clams_examinations', function (Blueprint $table) {
                $table->index('visit_id');
            });
        }

        if (Schema::hasTable('chest_examinations') && ! Schema::hasIndex('chest_examinations', 'chest_examinations_visit_id_index')) {
            Schema::table('chest_examinations', function (Blueprint $table) {
                $table->index('visit_id');
            });
        }

        if (Schema::hasTable('dental_examinations') && ! Schema::hasIndex('dental_examinations', 'dental_examinations_visit_id_index')) {
            Schema::table('dental_examinations', function (Blueprint $table) {
                $table->index('visit_id');
            });
        }

        if (Schema::hasTable('ear_examinations') && ! Schema::hasIndex('ear_examinations', 'ear_examinations_visit_id_index')) {
            Schema::table('ear_examinations', function (Blueprint $table) {
                $table->index('visit_id');
            });
        }

        if (Schema::hasTable('eeg_examinations') && ! Schema::hasIndex('eeg_examinations', 'eeg_examinations_visit_id_index')) {
            Schema::table('eeg_examinations', function (Blueprint $table) {
                $table->index('visit_id');
            });
        }

        if (Schema::hasTable('ekg_examinations') && ! Schema::hasIndex('ekg_examinations', 'ekg_examinations_visit_id_index')) {
            Schema::table('ekg_examinations', function (Blueprint $table) {
                $table->index('visit_id');
            });
        }

        if (Schema::hasTable('emg_examinations') && ! Schema::hasIndex('emg_examinations', 'emg_examinations_visit_id_index')) {
            Schema::table('emg_examinations', function (Blueprint $table) {
                $table->index('visit_id');
            });
        }

        if (Schema::hasTable('epfra_assessments') && ! Schema::hasIndex('epfra_assessments', 'epfra_assessments_visit_id_index')) {
            Schema::table('epfra_assessments', function (Blueprint $table) {
                $table->index('visit_id');
            });
        }

        if (Schema::hasTable('eye_examinations') && ! Schema::hasIndex('eye_examinations', 'eye_examinations_visit_id_index')) {
            Schema::table('eye_examinations', function (Blueprint $table) {
                $table->index('visit_id');
            });
        }

        if (Schema::hasTable('family_planning_obstetrics') && ! Schema::hasIndex('family_planning_obstetrics', 'family_planning_obstetrics_visit_id_index')) {
            Schema::table('family_planning_obstetrics', function (Blueprint $table) {
                $table->index('visit_id');
            });
        }

        if (Schema::hasTable('finger_examinations') && ! Schema::hasIndex('finger_examinations', 'finger_examinations_visit_id_index')) {
            Schema::table('finger_examinations', function (Blueprint $table) {
                $table->index('visit_id');
            });
        }

        if (Schema::hasTable('fingernail_examinations') && ! Schema::hasIndex('fingernail_examinations', 'fingernail_examinations_visit_id_index')) {
            Schema::table('fingernail_examinations', function (Blueprint $table) {
                $table->index('visit_id');
            });
        }

        if (Schema::hasTable('fluid_balance_assessments') && ! Schema::hasIndex('fluid_balance_assessments', 'fluid_balance_assessments_visit_id_index')) {
            Schema::table('fluid_balance_assessments', function (Blueprint $table) {
                $table->index('visit_id');
            });
        }

        if (Schema::hasTable('food_allergen_examinations') && ! Schema::hasIndex('food_allergen_examinations', 'food_allergen_examinations_visit_id_index')) {
            Schema::table('food_allergen_examinations', function (Blueprint $table) {
                $table->index('visit_id');
            });
        }

        if (Schema::hasTable('forearm_examinations') && ! Schema::hasIndex('forearm_examinations', 'forearm_examinations_visit_id_index')) {
            Schema::table('forearm_examinations', function (Blueprint $table) {
                $table->index('visit_id');
            });
        }

        if (Schema::hasTable('general_examinations') && ! Schema::hasIndex('general_examinations', 'general_examinations_visit_id_index')) {
            Schema::table('general_examinations', function (Blueprint $table) {
                $table->index('visit_id');
            });
        }

        if (Schema::hasTable('genital_examinations') && ! Schema::hasIndex('genital_examinations', 'genital_examinations_visit_id_index')) {
            Schema::table('genital_examinations', function (Blueprint $table) {
                $table->index('visit_id');
            });
        }

        if (Schema::hasTable('get_up_and_go_test_assessments') && ! Schema::hasIndex('get_up_and_go_test_assessments', 'get_up_and_go_test_assessments_visit_id_index')) {
            Schema::table('get_up_and_go_test_assessments', function (Blueprint $table) {
                $table->index('visit_id');
            });
        }

        if (Schema::hasTable('grace_risk_score_assessments') && ! Schema::hasIndex('grace_risk_score_assessments', 'grace_risk_score_assessments_visit_id_index')) {
            Schema::table('grace_risk_score_assessments', function (Blueprint $table) {
                $table->index('visit_id');
            });
        }

        if (Schema::hasTable('hair_examinations') && ! Schema::hasIndex('hair_examinations', 'hair_examinations_visit_id_index')) {
            Schema::table('hair_examinations', function (Blueprint $table) {
                $table->index('visit_id');
            });
        }

        if (Schema::hasTable('hand_joint_examinations') && ! Schema::hasIndex('hand_joint_examinations', 'hand_joint_examinations_visit_id_index')) {
            Schema::table('hand_joint_examinations', function (Blueprint $table) {
                $table->index('visit_id');
            });
        }

        if (Schema::hasTable('head_examinations') && ! Schema::hasIndex('head_examinations', 'head_examinations_visit_id_index')) {
            Schema::table('head_examinations', function (Blueprint $table) {
                $table->index('visit_id');
            });
        }

        if (Schema::hasTable('image_markers') && ! Schema::hasIndex('image_markers', 'image_markers_visit_id_index')) {
            Schema::table('image_markers', function (Blueprint $table) {
                $table->index('visit_id');
            });
        }

        if (Schema::hasTable('inhalant_allergen_examinations') && ! Schema::hasIndex('inhalant_allergen_examinations', 'inhalant_allergen_examinations_visit_id_index')) {
            Schema::table('inhalant_allergen_examinations', function (Blueprint $table) {
                $table->index('visit_id');
            });
        }

        if (Schema::hasTable('intradialytic_hd_monitorings') && ! Schema::hasIndex('intradialytic_hd_monitorings', 'intradialytic_hd_monitorings_visit_id_index')) {
            Schema::table('intradialytic_hd_monitorings', function (Blueprint $table) {
                $table->index('visit_id');
            });
        }

        if (Schema::hasTable('leg_joint_examinations') && ! Schema::hasIndex('leg_joint_examinations', 'leg_joint_examinations_visit_id_index')) {
            Schema::table('leg_joint_examinations', function (Blueprint $table) {
                $table->index('visit_id');
            });
        }

        if (Schema::hasTable('lip_examinations') && ! Schema::hasIndex('lip_examinations', 'lip_examinations_visit_id_index')) {
            Schema::table('lip_examinations', function (Blueprint $table) {
                $table->index('visit_id');
            });
        }

        if (Schema::hasTable('lower_gi_tract_examinations') && ! Schema::hasIndex('lower_gi_tract_examinations', 'lower_gi_tract_examinations_visit_id_index')) {
            Schema::table('lower_gi_tract_examinations', function (Blueprint $table) {
                $table->index('visit_id');
            });
        }

        if (Schema::hasTable('lower_leg_examinations') && ! Schema::hasIndex('lower_leg_examinations', 'lower_leg_examinations_visit_id_index')) {
            Schema::table('lower_leg_examinations', function (Blueprint $table) {
                $table->index('visit_id');
            });
        }

        if (Schema::hasTable('mchat_assessment_examinations') && ! Schema::hasIndex('mchat_assessment_examinations', 'mchat_assessment_examinations_visit_id_index')) {
            Schema::table('mchat_assessment_examinations', function (Blueprint $table) {
                $table->index('visit_id');
            });
        }

        if (Schema::hasTable('modified_barthel_index_assessments') && ! Schema::hasIndex('modified_barthel_index_assessments', 'modified_barthel_index_assessments_visit_id_index')) {
            Schema::table('modified_barthel_index_assessments', function (Blueprint $table) {
                $table->index('visit_id');
            });
        }

        if (Schema::hasTable('neck_examinations') && ! Schema::hasIndex('neck_examinations', 'neck_examinations_visit_id_index')) {
            Schema::table('neck_examinations', function (Blueprint $table) {
                $table->index('visit_id');
            });
        }

        if (Schema::hasTable('nose_examinations') && ! Schema::hasIndex('nose_examinations', 'nose_examinations_visit_id_index')) {
            Schema::table('nose_examinations', function (Blueprint $table) {
                $table->index('visit_id');
            });
        }

        if (Schema::hasTable('obstetrics_records') && ! Schema::hasIndex('obstetrics_records', 'obstetrics_records_visit_id_index')) {
            Schema::table('obstetrics_records', function (Blueprint $table) {
                $table->index('visit_id');
            });
        }

        if (Schema::hasTable('palate_examinations') && ! Schema::hasIndex('palate_examinations', 'palate_examinations_visit_id_index')) {
            Schema::table('palate_examinations', function (Blueprint $table) {
                $table->index('visit_id');
            });
        }

        if (Schema::hasTable('patient_transfer_sheets') && ! Schema::hasIndex('patient_transfer_sheets', 'patient_transfer_sheets_visit_id_index')) {
            Schema::table('patient_transfer_sheets', function (Blueprint $table) {
                $table->index('visit_id');
            });
        }

        if (Schema::hasTable('pharynx_examinations') && ! Schema::hasIndex('pharynx_examinations', 'pharynx_examinations_visit_id_index')) {
            Schema::table('pharynx_examinations', function (Blueprint $table) {
                $table->index('visit_id');
            });
        }

        if (Schema::hasTable('physical_assessments') && ! Schema::hasIndex('physical_assessments', 'physical_assessments_visit_id_index')) {
            Schema::table('physical_assessments', function (Blueprint $table) {
                $table->index('visit_id');
            });
        }

        if (Schema::hasTable('physical_examinations') && ! Schema::hasIndex('physical_examinations', 'physical_examinations_visit_id_index')) {
            Schema::table('physical_examinations', function (Blueprint $table) {
                $table->index('visit_id');
            });
        }

        if (Schema::hasTable('pressure_ulcer_risk_assessments') && ! Schema::hasIndex('pressure_ulcer_risk_assessments', 'pressure_ulcer_risk_assessments_visit_id_index')) {
            Schema::table('pressure_ulcer_risk_assessments', function (Blueprint $table) {
                $table->index('visit_id');
            });
        }

        if (Schema::hasTable('procedure_surgeries') && ! Schema::hasIndex('procedure_surgeries', 'procedure_surgeries_visit_id_index')) {
            Schema::table('procedure_surgeries', function (Blueprint $table) {
                $table->index('visit_id');
            });
        }

        if (Schema::hasTable('raven_test_examinations') && ! Schema::hasIndex('raven_test_examinations', 'raven_test_examinations_visit_id_index')) {
            Schema::table('raven_test_examinations', function (Blueprint $table) {
                $table->index('visit_id');
            });
        }

        if (Schema::hasTable('rehabilitation_procedure_examinations') && ! Schema::hasIndex('rehabilitation_procedure_examinations', 'rehabilitation_procedure_examinations_visit_id_index')) {
            Schema::table('rehabilitation_procedure_examinations', function (Blueprint $table) {
                $table->index('visit_id');
            });
        }

        if (Schema::hasTable('skin_prick_test_examinations') && ! Schema::hasIndex('skin_prick_test_examinations', 'skin_prick_test_examinations_visit_id_index')) {
            Schema::table('skin_prick_test_examinations', function (Blueprint $table) {
                $table->index('visit_id');
            });
        }

        if (Schema::hasTable('surgery_performers') && ! Schema::hasIndex('surgery_performers', 'surgery_performers_visit_id_index')) {
            Schema::table('surgery_performers', function (Blueprint $table) {
                $table->index('visit_id');
            });
        }

        if (Schema::hasTable('thigh_examinations') && ! Schema::hasIndex('thigh_examinations', 'thigh_examinations_visit_id_index')) {
            Schema::table('thigh_examinations', function (Blueprint $table) {
                $table->index('visit_id');
            });
        }

        if (Schema::hasTable('throat_examinations') && ! Schema::hasIndex('throat_examinations', 'throat_examinations_visit_id_index')) {
            Schema::table('throat_examinations', function (Blueprint $table) {
                $table->index('visit_id');
            });
        }

        if (Schema::hasTable('toe_examinations') && ! Schema::hasIndex('toe_examinations', 'toe_examinations_visit_id_index')) {
            Schema::table('toe_examinations', function (Blueprint $table) {
                $table->index('visit_id');
            });
        }

        if (Schema::hasTable('toenail_examinations') && ! Schema::hasIndex('toenail_examinations', 'toenail_examinations_visit_id_index')) {
            Schema::table('toenail_examinations', function (Blueprint $table) {
                $table->index('visit_id');
            });
        }

        if (Schema::hasTable('tongue_examinations') && ! Schema::hasIndex('tongue_examinations', 'tongue_examinations_visit_id_index')) {
            Schema::table('tongue_examinations', function (Blueprint $table) {
                $table->index('visit_id');
            });
        }

        if (Schema::hasTable('tonsil_examinations') && ! Schema::hasIndex('tonsil_examinations', 'tonsil_examinations_visit_id_index')) {
            Schema::table('tonsil_examinations', function (Blueprint $table) {
                $table->index('visit_id');
            });
        }

        if (Schema::hasTable('transcranial_doppler_examinations') && ! Schema::hasIndex('transcranial_doppler_examinations', 'transcranial_doppler_examinations_visit_id_index')) {
            Schema::table('transcranial_doppler_examinations', function (Blueprint $table) {
                $table->index('visit_id');
            });
        }

        if (Schema::hasTable('upper_arm_examinations') && ! Schema::hasIndex('upper_arm_examinations', 'upper_arm_examinations_visit_id_index')) {
            Schema::table('upper_arm_examinations', function (Blueprint $table) {
                $table->index('visit_id');
            });
        }

        if (Schema::hasTable('upper_gi_tract_examinations') && ! Schema::hasIndex('upper_gi_tract_examinations', 'upper_gi_tract_examinations_visit_id_index')) {
            Schema::table('upper_gi_tract_examinations', function (Blueprint $table) {
                $table->index('visit_id');
            });
        }

    }

    public function down(): void
    {
        if (Schema::hasTable('abdomen_examinations') && Schema::hasIndex('abdomen_examinations', 'abdomen_examinations_visit_id_index')) {
            Schema::table('abdomen_examinations', function (Blueprint $table) {
                $table->dropIndex('abdomen_examinations_visit_id_index');
            });
        }

        if (Schema::hasTable('anal_examinations') && Schema::hasIndex('anal_examinations', 'anal_examinations_visit_id_index')) {
            Schema::table('anal_examinations', function (Blueprint $table) {
                $table->dropIndex('anal_examinations_visit_id_index');
            });
        }

        if (Schema::hasTable('back_examinations') && Schema::hasIndex('back_examinations', 'back_examinations_visit_id_index')) {
            Schema::table('back_examinations', function (Blueprint $table) {
                $table->dropIndex('back_examinations_visit_id_index');
            });
        }

        if (Schema::hasTable('barthel_index_assessments') && Schema::hasIndex('barthel_index_assessments', 'barthel_index_assessments_visit_id_index')) {
            Schema::table('barthel_index_assessments', function (Blueprint $table) {
                $table->dropIndex('barthel_index_assessments_visit_id_index');
            });
        }

        if (Schema::hasTable('breast_examinations') && Schema::hasIndex('breast_examinations', 'breast_examinations_visit_id_index')) {
            Schema::table('breast_examinations', function (Blueprint $table) {
                $table->dropIndex('breast_examinations_visit_id_index');
            });
        }

        if (Schema::hasTable('case_manager_assessments') && Schema::hasIndex('case_manager_assessments', 'case_manager_assessments_visit_id_index')) {
            Schema::table('case_manager_assessments', function (Blueprint $table) {
                $table->dropIndex('case_manager_assessments_visit_id_index');
            });
        }

        if (Schema::hasTable('cat_clams_examinations') && Schema::hasIndex('cat_clams_examinations', 'cat_clams_examinations_visit_id_index')) {
            Schema::table('cat_clams_examinations', function (Blueprint $table) {
                $table->dropIndex('cat_clams_examinations_visit_id_index');
            });
        }

        if (Schema::hasTable('chest_examinations') && Schema::hasIndex('chest_examinations', 'chest_examinations_visit_id_index')) {
            Schema::table('chest_examinations', function (Blueprint $table) {
                $table->dropIndex('chest_examinations_visit_id_index');
            });
        }

        if (Schema::hasTable('dental_examinations') && Schema::hasIndex('dental_examinations', 'dental_examinations_visit_id_index')) {
            Schema::table('dental_examinations', function (Blueprint $table) {
                $table->dropIndex('dental_examinations_visit_id_index');
            });
        }

        if (Schema::hasTable('ear_examinations') && Schema::hasIndex('ear_examinations', 'ear_examinations_visit_id_index')) {
            Schema::table('ear_examinations', function (Blueprint $table) {
                $table->dropIndex('ear_examinations_visit_id_index');
            });
        }

        if (Schema::hasTable('eeg_examinations') && Schema::hasIndex('eeg_examinations', 'eeg_examinations_visit_id_index')) {
            Schema::table('eeg_examinations', function (Blueprint $table) {
                $table->dropIndex('eeg_examinations_visit_id_index');
            });
        }

        if (Schema::hasTable('ekg_examinations') && Schema::hasIndex('ekg_examinations', 'ekg_examinations_visit_id_index')) {
            Schema::table('ekg_examinations', function (Blueprint $table) {
                $table->dropIndex('ekg_examinations_visit_id_index');
            });
        }

        if (Schema::hasTable('emg_examinations') && Schema::hasIndex('emg_examinations', 'emg_examinations_visit_id_index')) {
            Schema::table('emg_examinations', function (Blueprint $table) {
                $table->dropIndex('emg_examinations_visit_id_index');
            });
        }

        if (Schema::hasTable('epfra_assessments') && Schema::hasIndex('epfra_assessments', 'epfra_assessments_visit_id_index')) {
            Schema::table('epfra_assessments', function (Blueprint $table) {
                $table->dropIndex('epfra_assessments_visit_id_index');
            });
        }

        if (Schema::hasTable('eye_examinations') && Schema::hasIndex('eye_examinations', 'eye_examinations_visit_id_index')) {
            Schema::table('eye_examinations', function (Blueprint $table) {
                $table->dropIndex('eye_examinations_visit_id_index');
            });
        }

        if (Schema::hasTable('family_planning_obstetrics') && Schema::hasIndex('family_planning_obstetrics', 'family_planning_obstetrics_visit_id_index')) {
            Schema::table('family_planning_obstetrics', function (Blueprint $table) {
                $table->dropIndex('family_planning_obstetrics_visit_id_index');
            });
        }

        if (Schema::hasTable('finger_examinations') && Schema::hasIndex('finger_examinations', 'finger_examinations_visit_id_index')) {
            Schema::table('finger_examinations', function (Blueprint $table) {
                $table->dropIndex('finger_examinations_visit_id_index');
            });
        }

        if (Schema::hasTable('fingernail_examinations') && Schema::hasIndex('fingernail_examinations', 'fingernail_examinations_visit_id_index')) {
            Schema::table('fingernail_examinations', function (Blueprint $table) {
                $table->dropIndex('fingernail_examinations_visit_id_index');
            });
        }

        if (Schema::hasTable('fluid_balance_assessments') && Schema::hasIndex('fluid_balance_assessments', 'fluid_balance_assessments_visit_id_index')) {
            Schema::table('fluid_balance_assessments', function (Blueprint $table) {
                $table->dropIndex('fluid_balance_assessments_visit_id_index');
            });
        }

        if (Schema::hasTable('food_allergen_examinations') && Schema::hasIndex('food_allergen_examinations', 'food_allergen_examinations_visit_id_index')) {
            Schema::table('food_allergen_examinations', function (Blueprint $table) {
                $table->dropIndex('food_allergen_examinations_visit_id_index');
            });
        }

        if (Schema::hasTable('forearm_examinations') && Schema::hasIndex('forearm_examinations', 'forearm_examinations_visit_id_index')) {
            Schema::table('forearm_examinations', function (Blueprint $table) {
                $table->dropIndex('forearm_examinations_visit_id_index');
            });
        }

        if (Schema::hasTable('general_examinations') && Schema::hasIndex('general_examinations', 'general_examinations_visit_id_index')) {
            Schema::table('general_examinations', function (Blueprint $table) {
                $table->dropIndex('general_examinations_visit_id_index');
            });
        }

        if (Schema::hasTable('genital_examinations') && Schema::hasIndex('genital_examinations', 'genital_examinations_visit_id_index')) {
            Schema::table('genital_examinations', function (Blueprint $table) {
                $table->dropIndex('genital_examinations_visit_id_index');
            });
        }

        if (Schema::hasTable('get_up_and_go_test_assessments') && Schema::hasIndex('get_up_and_go_test_assessments', 'get_up_and_go_test_assessments_visit_id_index')) {
            Schema::table('get_up_and_go_test_assessments', function (Blueprint $table) {
                $table->dropIndex('get_up_and_go_test_assessments_visit_id_index');
            });
        }

        if (Schema::hasTable('grace_risk_score_assessments') && Schema::hasIndex('grace_risk_score_assessments', 'grace_risk_score_assessments_visit_id_index')) {
            Schema::table('grace_risk_score_assessments', function (Blueprint $table) {
                $table->dropIndex('grace_risk_score_assessments_visit_id_index');
            });
        }

        if (Schema::hasTable('hair_examinations') && Schema::hasIndex('hair_examinations', 'hair_examinations_visit_id_index')) {
            Schema::table('hair_examinations', function (Blueprint $table) {
                $table->dropIndex('hair_examinations_visit_id_index');
            });
        }

        if (Schema::hasTable('hand_joint_examinations') && Schema::hasIndex('hand_joint_examinations', 'hand_joint_examinations_visit_id_index')) {
            Schema::table('hand_joint_examinations', function (Blueprint $table) {
                $table->dropIndex('hand_joint_examinations_visit_id_index');
            });
        }

        if (Schema::hasTable('head_examinations') && Schema::hasIndex('head_examinations', 'head_examinations_visit_id_index')) {
            Schema::table('head_examinations', function (Blueprint $table) {
                $table->dropIndex('head_examinations_visit_id_index');
            });
        }

        if (Schema::hasTable('image_markers') && Schema::hasIndex('image_markers', 'image_markers_visit_id_index')) {
            Schema::table('image_markers', function (Blueprint $table) {
                $table->dropIndex('image_markers_visit_id_index');
            });
        }

        if (Schema::hasTable('inhalant_allergen_examinations') && Schema::hasIndex('inhalant_allergen_examinations', 'inhalant_allergen_examinations_visit_id_index')) {
            Schema::table('inhalant_allergen_examinations', function (Blueprint $table) {
                $table->dropIndex('inhalant_allergen_examinations_visit_id_index');
            });
        }

        if (Schema::hasTable('intradialytic_hd_monitorings') && Schema::hasIndex('intradialytic_hd_monitorings', 'intradialytic_hd_monitorings_visit_id_index')) {
            Schema::table('intradialytic_hd_monitorings', function (Blueprint $table) {
                $table->dropIndex('intradialytic_hd_monitorings_visit_id_index');
            });
        }

        if (Schema::hasTable('leg_joint_examinations') && Schema::hasIndex('leg_joint_examinations', 'leg_joint_examinations_visit_id_index')) {
            Schema::table('leg_joint_examinations', function (Blueprint $table) {
                $table->dropIndex('leg_joint_examinations_visit_id_index');
            });
        }

        if (Schema::hasTable('lip_examinations') && Schema::hasIndex('lip_examinations', 'lip_examinations_visit_id_index')) {
            Schema::table('lip_examinations', function (Blueprint $table) {
                $table->dropIndex('lip_examinations_visit_id_index');
            });
        }

        if (Schema::hasTable('lower_gi_tract_examinations') && Schema::hasIndex('lower_gi_tract_examinations', 'lower_gi_tract_examinations_visit_id_index')) {
            Schema::table('lower_gi_tract_examinations', function (Blueprint $table) {
                $table->dropIndex('lower_gi_tract_examinations_visit_id_index');
            });
        }

        if (Schema::hasTable('lower_leg_examinations') && Schema::hasIndex('lower_leg_examinations', 'lower_leg_examinations_visit_id_index')) {
            Schema::table('lower_leg_examinations', function (Blueprint $table) {
                $table->dropIndex('lower_leg_examinations_visit_id_index');
            });
        }

        if (Schema::hasTable('mchat_assessment_examinations') && Schema::hasIndex('mchat_assessment_examinations', 'mchat_assessment_examinations_visit_id_index')) {
            Schema::table('mchat_assessment_examinations', function (Blueprint $table) {
                $table->dropIndex('mchat_assessment_examinations_visit_id_index');
            });
        }

        if (Schema::hasTable('modified_barthel_index_assessments') && Schema::hasIndex('modified_barthel_index_assessments', 'modified_barthel_index_assessments_visit_id_index')) {
            Schema::table('modified_barthel_index_assessments', function (Blueprint $table) {
                $table->dropIndex('modified_barthel_index_assessments_visit_id_index');
            });
        }

        if (Schema::hasTable('neck_examinations') && Schema::hasIndex('neck_examinations', 'neck_examinations_visit_id_index')) {
            Schema::table('neck_examinations', function (Blueprint $table) {
                $table->dropIndex('neck_examinations_visit_id_index');
            });
        }

        if (Schema::hasTable('nose_examinations') && Schema::hasIndex('nose_examinations', 'nose_examinations_visit_id_index')) {
            Schema::table('nose_examinations', function (Blueprint $table) {
                $table->dropIndex('nose_examinations_visit_id_index');
            });
        }

        if (Schema::hasTable('obstetrics_records') && Schema::hasIndex('obstetrics_records', 'obstetrics_records_visit_id_index')) {
            Schema::table('obstetrics_records', function (Blueprint $table) {
                $table->dropIndex('obstetrics_records_visit_id_index');
            });
        }

        if (Schema::hasTable('palate_examinations') && Schema::hasIndex('palate_examinations', 'palate_examinations_visit_id_index')) {
            Schema::table('palate_examinations', function (Blueprint $table) {
                $table->dropIndex('palate_examinations_visit_id_index');
            });
        }

        if (Schema::hasTable('patient_transfer_sheets') && Schema::hasIndex('patient_transfer_sheets', 'patient_transfer_sheets_visit_id_index')) {
            Schema::table('patient_transfer_sheets', function (Blueprint $table) {
                $table->dropIndex('patient_transfer_sheets_visit_id_index');
            });
        }

        if (Schema::hasTable('pharynx_examinations') && Schema::hasIndex('pharynx_examinations', 'pharynx_examinations_visit_id_index')) {
            Schema::table('pharynx_examinations', function (Blueprint $table) {
                $table->dropIndex('pharynx_examinations_visit_id_index');
            });
        }

        if (Schema::hasTable('physical_assessments') && Schema::hasIndex('physical_assessments', 'physical_assessments_visit_id_index')) {
            Schema::table('physical_assessments', function (Blueprint $table) {
                $table->dropIndex('physical_assessments_visit_id_index');
            });
        }

        if (Schema::hasTable('physical_examinations') && Schema::hasIndex('physical_examinations', 'physical_examinations_visit_id_index')) {
            Schema::table('physical_examinations', function (Blueprint $table) {
                $table->dropIndex('physical_examinations_visit_id_index');
            });
        }

        if (Schema::hasTable('pressure_ulcer_risk_assessments') && Schema::hasIndex('pressure_ulcer_risk_assessments', 'pressure_ulcer_risk_assessments_visit_id_index')) {
            Schema::table('pressure_ulcer_risk_assessments', function (Blueprint $table) {
                $table->dropIndex('pressure_ulcer_risk_assessments_visit_id_index');
            });
        }

        if (Schema::hasTable('procedure_surgeries') && Schema::hasIndex('procedure_surgeries', 'procedure_surgeries_visit_id_index')) {
            Schema::table('procedure_surgeries', function (Blueprint $table) {
                $table->dropIndex('procedure_surgeries_visit_id_index');
            });
        }

        if (Schema::hasTable('raven_test_examinations') && Schema::hasIndex('raven_test_examinations', 'raven_test_examinations_visit_id_index')) {
            Schema::table('raven_test_examinations', function (Blueprint $table) {
                $table->dropIndex('raven_test_examinations_visit_id_index');
            });
        }

        if (Schema::hasTable('rehabilitation_procedure_examinations') && Schema::hasIndex('rehabilitation_procedure_examinations', 'rehabilitation_procedure_examinations_visit_id_index')) {
            Schema::table('rehabilitation_procedure_examinations', function (Blueprint $table) {
                $table->dropIndex('rehabilitation_procedure_examinations_visit_id_index');
            });
        }

        if (Schema::hasTable('skin_prick_test_examinations') && Schema::hasIndex('skin_prick_test_examinations', 'skin_prick_test_examinations_visit_id_index')) {
            Schema::table('skin_prick_test_examinations', function (Blueprint $table) {
                $table->dropIndex('skin_prick_test_examinations_visit_id_index');
            });
        }

        if (Schema::hasTable('surgery_performers') && Schema::hasIndex('surgery_performers', 'surgery_performers_visit_id_index')) {
            Schema::table('surgery_performers', function (Blueprint $table) {
                $table->dropIndex('surgery_performers_visit_id_index');
            });
        }

        if (Schema::hasTable('thigh_examinations') && Schema::hasIndex('thigh_examinations', 'thigh_examinations_visit_id_index')) {
            Schema::table('thigh_examinations', function (Blueprint $table) {
                $table->dropIndex('thigh_examinations_visit_id_index');
            });
        }

        if (Schema::hasTable('throat_examinations') && Schema::hasIndex('throat_examinations', 'throat_examinations_visit_id_index')) {
            Schema::table('throat_examinations', function (Blueprint $table) {
                $table->dropIndex('throat_examinations_visit_id_index');
            });
        }

        if (Schema::hasTable('toe_examinations') && Schema::hasIndex('toe_examinations', 'toe_examinations_visit_id_index')) {
            Schema::table('toe_examinations', function (Blueprint $table) {
                $table->dropIndex('toe_examinations_visit_id_index');
            });
        }

        if (Schema::hasTable('toenail_examinations') && Schema::hasIndex('toenail_examinations', 'toenail_examinations_visit_id_index')) {
            Schema::table('toenail_examinations', function (Blueprint $table) {
                $table->dropIndex('toenail_examinations_visit_id_index');
            });
        }

        if (Schema::hasTable('tongue_examinations') && Schema::hasIndex('tongue_examinations', 'tongue_examinations_visit_id_index')) {
            Schema::table('tongue_examinations', function (Blueprint $table) {
                $table->dropIndex('tongue_examinations_visit_id_index');
            });
        }

        if (Schema::hasTable('tonsil_examinations') && Schema::hasIndex('tonsil_examinations', 'tonsil_examinations_visit_id_index')) {
            Schema::table('tonsil_examinations', function (Blueprint $table) {
                $table->dropIndex('tonsil_examinations_visit_id_index');
            });
        }

        if (Schema::hasTable('transcranial_doppler_examinations') && Schema::hasIndex('transcranial_doppler_examinations', 'transcranial_doppler_examinations_visit_id_index')) {
            Schema::table('transcranial_doppler_examinations', function (Blueprint $table) {
                $table->dropIndex('transcranial_doppler_examinations_visit_id_index');
            });
        }

        if (Schema::hasTable('upper_arm_examinations') && Schema::hasIndex('upper_arm_examinations', 'upper_arm_examinations_visit_id_index')) {
            Schema::table('upper_arm_examinations', function (Blueprint $table) {
                $table->dropIndex('upper_arm_examinations_visit_id_index');
            });
        }

        if (Schema::hasTable('upper_gi_tract_examinations') && Schema::hasIndex('upper_gi_tract_examinations', 'upper_gi_tract_examinations_visit_id_index')) {
            Schema::table('upper_gi_tract_examinations', function (Blueprint $table) {
                $table->dropIndex('upper_gi_tract_examinations_visit_id_index');
            });
        }

    }
};
