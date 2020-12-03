#   IMPORTS
import glob
#   import pip
#   pip.main(['install', 'mysql-connector-python-rf'])
import math

import mysql.connector
import pandas as pd


def main():
    again = True
    while again:
        printMenu()
        prompt = 'Please select what data you would like to compile and insert: '
        choice = int(input(prompt))
        if choice == 1:
            process_universities(get_schools(), get_countries())
        elif choice == 2:
            generate_semesters()
        elif choice == 3:
            process_rate_my_professor(get_states())
        elif choice == 4:
            process_us_universities(get_states())
        elif choice == 100:
            again = False
    print('Program ending...')


def printMenu():
    print('1.  \tUniversities and Countries')
    print('2.  \tGenerate semesters')
    print('3.  \tRate My Professors')
    print('4.  \tUS Universities and States')
    print('100.\tQuit')


def get_schools():
    schools_lists = glob.glob('data/school/*.csv')

    schools = {}

    for schools_list in schools_lists:
        schools[schools_list] = pd.read_csv(schools_list)

    return schools


def get_countries():
    countries_lists = glob.glob('data/country/*.csv')

    countries = {}

    for countries_list in countries_lists:
        countries[countries_list] = pd.read_csv(countries_list)

    return countries


def get_states():
    state_file = 'data/country/populations.csv'
    state_data = pd.read_csv(state_file)

    del state_data['population']

    return state_data


def process_universities(schools, countries):
    desired_column_names = ['country', 'school name', 'school_name', 'institution', 'college', 'university']
    longest_frame_size = 0
    longest_frame_name = ''

    #   Get rid of the unnecessary columns and normalize the column names
    for element in schools:
        #   print(list(schools[element].columns))
        if len(schools[element].index) > longest_frame_size:
            longest_frame_size = len(schools[element].index)
            longest_frame_name = element

        for col in schools[element].columns:
            if str.lower(col) not in desired_column_names:
                schools[element].drop([col], axis=1, inplace=True)
            if str.lower(col) == 'country':
                schools[element].rename(columns={col: 'country'}, inplace=True)
            elif str.lower(col) in desired_column_names and str.lower(col) != 'country':
                schools[element].rename(columns={col: 'university'}, inplace=True)

        print(list(schools[element].columns))

    export_universities_to_mysql(schools[longest_frame_name])

    #   frame_names = schools.keys()
    #   print(longest_frame_name + ' has ' + str(longest_frame_size) + ' rows')
    #   merged_schools = schools[longest_frame_name]
    schools[longest_frame_name].to_csv('data/cleaned_longest_school_file.csv', index=False)
    '''
    if len(schools) > 1:
        for element in schools:
            if element != longest_frame_name:
                merged_schools = pd.merge(merged_schools, schools[element], on='university')

    column_sizes = merged_schools.max()
    column_len = [len(x) for x in column_sizes]
    max_size = min(column_len)
    print(max_size)
    print(column_len)
    column_names = merged_schools.columns.to_list()
    for i in range(0, len(column_sizes)):
        if column_names[i] != 'university' and column_len[i] != max_size:
            merged_schools.drop(columns=[column_names[i]], inplace=True)

    for col in merged_schools.columns:
        if col != 'university':
            merged_schools.rename(columns={col: 'country'}, inplace=True)
    print(merged_schools.columns)

    merged_schools.to_csv('data/cleaned_schools.csv', index=False)

    print('Ending...')
'''


def export_universities_to_mysql(info):
    conn = mysql.connector.connect(user='admin_nana@ct-textbooks-server', password='zIvre4-wucsez-qokfyj',
                                   host='ct-textbooks-server.mysql.database.azure.com', database='TextbookDB',
                                   ssl_ca='/Users/nana/Github/Create_Table_Textbooks/ssl/BaltimoreCyberTrustRoot.crt.pem')
    cursor = conn.cursor()
    for i in range(len(info)):
        name, country = info.loc[i, 'university'], info.loc[i, 'country']
        sql_query = """CALL InsertUniversityFromPython(%s, %s)"""
        data = (name, country)
        try:
            cursor.execute(sql_query, data)
            conn.commit()
        except:
            conn.rollback()
    conn.close()


def generate_semesters():
    conn = mysql.connector.connect(user='admin_nana@ct-textbooks-server', password='zIvre4-wucsez-qokfyj',
                                   host='ct-textbooks-server.mysql.database.azure.com', database='TextbookDB',
                                   ssl_ca='/Users/nana/Github/Create_Table_Textbooks/ssl/BaltimoreCyberTrustRoot.crt.pem')
    cursor = conn.cursor()
    terms = ['FALL', 'SPRING', 'SUMMER', 'MAY']
    for year in range(1900, 3000, 1):
        for term in terms:
            sql_query = """CALL InsertSemester(%s, %s)"""
            data = (term, year)
            try:
                cursor.execute(sql_query, data)
                conn.commit()
            except:
                conn.rollback()

    conn.close()


def process_rate_my_professor(states):
    professors_files = glob.glob('data/professors/RateMyProfessor SampleData, Contact hejibo@usee.tech for the whole 5G dataset/*.csv')
    professors_files.append('data/professors/RateMyProfessor_Sample data.csv')

    print(len(professors_files))

    professors = {}
    desired_column_names = ['professor_name', 'school_name', 'state_name']
    all_professors = pd.DataFrame(columns=desired_column_names)

    for professor_file in professors_files:
        professors[professor_file] = pd.read_csv(professor_file)
        for col in professors[professor_file].columns:
            if str.lower(col) not in desired_column_names:
                professors[professor_file].drop([col], axis=1, inplace=True)
        professors[professor_file].drop_duplicates(subset=desired_column_names, inplace=True)
        all_professors = all_professors.append(professors[professor_file], ignore_index=True)
    all_professors.drop_duplicates(subset=desired_column_names, inplace=True)

    updated_data = pd.merge(all_professors, states, left_on='state_name', right_on='code')
    del updated_data['state_name']
    del updated_data['code']

    success = 0
    failure = 0

    updated_data.rename(columns={'statename': 'State'}, inplace=True)

    print(len(all_professors))

    conn = mysql.connector.connect(user='admin_nana@ct-textbooks-server', password='zIvre4-wucsez-qokfyj',
                                   host='ct-textbooks-server.mysql.database.azure.com', database='TextbookDB',
                                   ssl_ca='/Users/nana/Github/Create_Table_Textbooks/ssl/BaltimoreCyberTrustRoot.crt.pem')

    cursor = conn.cursor()

    for i in range(len(all_professors)):
        name, university, state = all_professors.loc[i, 'professor_name'].split(), all_professors.loc[i, 'school_name'], all_professors.loc[i, 'state_name']
        first_name = str(' '.join(name[0:-1]))
        last_name = name[-1]
        sql_query = """CALL InsertRateMyProfessor(%s, %s, %s, %s)"""
        #   InsertRateMyProfessor('Mimi', 'Kline', 'Bucks County Community College', ' PA');
        data = (first_name, last_name, university, state)
        try:
            cursor.execute(sql_query, data)
            conn.commit()
            success += 1
        except:
            conn.rollback()
            failure += 1
            break

        #print(math.floor((i / len(all_professors)) * 100))
        if i == math.floor((len(all_professors) / 2)):
            print("Half way there!")
        elif i == math.floor((len(all_professors) / 3)):
            print("33%")
        elif i == math.floor((len(all_professors) / 3) * 2):
            print("66%")
        elif math.floor((i / len(all_professors)) * 100) == 47:
            print("I'm 47% through with kicking your ass")
        elif i == 500:
            print("I would insert 500 files")
        elif i == 1000:
            print("I would insert 500 more, just to be the code that added 1000 files to end up in your db")

    conn.close()
    print("done")
    print("Successes: " + str(success) + " Failures: " + str(failure))


def process_us_universities(states):
    universities_file = 'data/school/Colleges_and_Universities_Campuses.csv'
    universities_data = pd.read_csv(universities_file)
    updated_data = pd.merge(universities_data, states, left_on='State', right_on='code', how='inner')
    del updated_data['State']
    del updated_data['code']

    success = 0
    failure = 0

    updated_data.rename(columns={'statename': 'State'}, inplace=True)

    #   schools[longest_frame_name].to_csv('data/cleaned_longest_school_file.csv', index=False)

    conn = mysql.connector.connect(user='admin_nana@ct-textbooks-server', password='zIvre4-wucsez-qokfyj',
                                   host='ct-textbooks-server.mysql.database.azure.com', database='TextbookDB',
                                   ssl_ca='/Users/nana/Github/Create_Table_Textbooks/ssl/BaltimoreCyberTrustRoot.crt.pem')
    cursor = conn.cursor()
    for i in range(len(updated_data)):
        name, address, city, state, area = updated_data.loc[i, 'Name'], updated_data.loc[i, 'Street'], updated_data.loc[i, 'City'],\
                        updated_data.loc[i, 'State'], updated_data.loc[i, 'AreaCode']
        sql_query = """CALL InsertUSUniversityFromPython(%s, %s, %s, %s, %s)"""
        data = (name, address, city, state, str(area))

        try:
            cursor.execute(sql_query, data)
            conn.commit()
            success += 1
        except:
            conn.rollback()
            failure += 1
            break

        print(math.floor((i / len(updated_data)) * 100))
        if i == math.floor((len(updated_data) / 2)):
            print("Half way there!")
        elif i == math.floor((len(updated_data) / 3)):
            print("33%")
        elif i == math.floor((len(updated_data) / 3) * 2):
            print("66%")

    conn.close()
    print("done")
    print("Successes: " + str(success) + " Failures: " + str(failure))


main()
