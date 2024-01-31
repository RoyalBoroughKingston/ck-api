from troposphere import Condition, Equals, Ref

def create_fortinet_managed_rules_all_rules_group_condition(template, fortinet_managed_rules_all_rules_group_parameter):
    return template.add_condition(
        "FortinetManagedRulesAllRulesGroupEnabled",
        Equals(Ref(fortinet_managed_rules_all_rules_group_parameter), 'Enabled')
    )


def create_aws_managed_rules_common_rules_group_condition(template, aws_managed_rules_common_rules_group_parameter):
    return template.add_condition(
        "AWSManagedRulesCommonRulesGroupEnabled",
        Equals(Ref(aws_managed_rules_common_rules_group_parameter), 'Enabled')
    )
