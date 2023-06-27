from troposphere import Template as BaseTemplate


def create_template(api_name):
    template = BaseTemplate(
        'Create the infrastructure needed to run the ' + api_name + ' API')
    template.add_version('2010-09-09')

    return template
